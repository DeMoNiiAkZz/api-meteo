<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Chemin vers le fichier JSON qui stockera les alertes
$jsonFile = 'alertes.json';

// Fonction pour lire les alertes depuis le fichier JSON
function readAlertes() {
    global $jsonFile;
    
    if (!file_exists($jsonFile)) {
        // Créer un fichier avec des données initiales si le fichier n'existe pas
        $initialData = [
            1 => [
                "id" => 1,
                "alerte" => "Vague de tempête",
                "niveau" => "Rouge",
                "description" => "Des vents violents et des vagues importantes sont attendus.",
                "zone" => "Côte Normande",
                "debut" => "2023-10-01 14:00:00",
                "fin" => "2023-10-02 18:00:00"
            ],
            2 => [
                "id" => 2,
                "alerte" => "Marée haute",
                "niveau" => "Orange",
                "description" => "Risque d'inondation dans les zones basses.",
                "zone" => "Bretagne",
                "debut" => "2023-10-05 10:00:00",
                "fin" => "2023-10-05 15:00:00"
            ]
        ];
        
        file_put_contents($jsonFile, json_encode($initialData, JSON_PRETTY_PRINT));
        return $initialData;
    }
    
    $jsonContent = file_get_contents($jsonFile);
    return json_decode($jsonContent, true);
}

// Fonction pour écrire les alertes dans le fichier JSON
function writeAlertes($alertes) {
    global $jsonFile;
    file_put_contents($jsonFile, json_encode($alertes, JSON_PRETTY_PRINT));
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Charger les alertes depuis le fichier JSON
$alertes = readAlertes();

switch ($method) {
    case 'GET':
        if ($id !== null && isset($alertes[$id])) {
            echo json_encode([
                "success" => true,
                "alertes" => [$alertes[$id]]
            ], JSON_PRETTY_PRINT);
        } elseif ($id !== null) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Alerte non trouvée"
            ]);
        } else {
            echo json_encode([
                "success" => true,
                "alertes" => array_values($alertes)
            ], JSON_PRETTY_PRINT);
        }
        break;

    case 'POST':
        // Générer un nouvel ID (le plus grand ID actuel + 1)
        $newId = empty($alertes) ? 1 : max(array_keys($alertes)) + 1;
        $input['id'] = $newId;
        $alertes[$newId] = $input;
        
        // Enregistrer les modifications dans le fichier JSON
        writeAlertes($alertes);
        
        echo json_encode([
            "success" => true,
            "message" => "Alerte créée",
            "data" => $alertes[$newId]
        ], JSON_PRETTY_PRINT);
        break;

    case 'PUT':
        if ($id !== null && isset($alertes[$id])) {
            // S'assurer que l'ID reste le même
            $input['id'] = $id;
            $alertes[$id] = array_merge($alertes[$id], $input);
            
            // Enregistrer les modifications dans le fichier JSON
            writeAlertes($alertes);
            
            echo json_encode([
                "success" => true,
                "message" => "Alerte mise à jour",
                "data" => $alertes[$id]
            ], JSON_PRETTY_PRINT);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Alerte à mettre à jour introuvable"
            ]);
        }
        break;

    case 'DELETE':
        if ($id !== null && isset($alertes[$id])) {
            unset($alertes[$id]);
            
            // Enregistrer les modifications dans le fichier JSON
            writeAlertes($alertes);
            
            echo json_encode([
                "success" => true,
                "message" => "Alerte supprimée"
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Alerte à supprimer introuvable"
            ]);
        }
        break;

    case 'OPTIONS':
        http_response_code(204);
        break;

    default:
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        break;
}
?>