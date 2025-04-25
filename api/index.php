<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

include 'db.php';

$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);
$id = isset($_GET["id"]) ? intval($_GET["id"]) : null;

switch ($method) {
    case "GET":
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM alertes WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            echo json_encode(["success" => true, "alertes" => [$result]]);
        } else {
            $result = $conn->query("SELECT * FROM alertes");
            $data = $result->fetch_all(MYSQLI_ASSOC);
            echo json_encode(["success" => true, "alertes" => $data]);
        }
        break;

    case "POST":
        $stmt = $conn->prepare("INSERT INTO alertes (alerte, niveau, description, zone, debut, fin) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $input["alerte"], $input["niveau"], $input["description"], $input["zone"], $input["debut"], $input["fin"]);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Alerte créée", "id" => $stmt->insert_id]);
        break;

    case "PUT":
        if (!$id) {
            echo json_encode(["success" => false, "message" => "ID manquant"]);
            exit;
        }
        $stmt = $conn->prepare("UPDATE alertes SET alerte=?, niveau=?, description=?, zone=?, debut=?, fin=? WHERE id=?");
        $stmt->bind_param("ssssssi", $input["alerte"], $input["niveau"], $input["description"], $input["zone"], $input["debut"], $input["fin"], $id);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Alerte mise à jour"]);
        break;

    case "DELETE":
        if (!$id) {
            echo json_encode(["success" => false, "message" => "ID manquant"]);
            exit;
        }
        $stmt = $conn->prepare("DELETE FROM alertes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Alerte supprimée"]);
        break;

    case "OPTIONS":
        http_response_code(204);
        break;

    default:
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Méthode non autorisée"]);
}
