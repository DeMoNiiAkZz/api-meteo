<?php
// Lire les paramètres depuis le fichier de configuration config.ini
$config = parse_ini_file('config.ini', true);

// Récupérer les informations de connexion à la base de données
$host = $config['database']['host'];
$user = $config['database']['user'];
$pass = $config['database']['pass'];
$dbname = $config['database']['name'];

// Connexion à la base de données MySQL
$conn = new mysqli($host, $user, $pass, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Échec de la connexion à la base de données : " . $conn->connect_error
    ]));
}

// SQL pour créer la table alertes si elle n'existe pas
$createTableSQL = "
    CREATE TABLE IF NOT EXISTS alertes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        alerte VARCHAR(255),
        niveau VARCHAR(50),
        zone VARCHAR(255),
        debut DATETIME,
        fin DATETIME,
        description TEXT
    );
";

if ($conn->query($createTableSQL) === TRUE) {
    echo json_encode([
        "success" => true,
        "message" => "Table 'alertes' vérifiée ou créée avec succès."
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la création de la table : " . $conn->error
    ]);
}

$conn->close();
?>
