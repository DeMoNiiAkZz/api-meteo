<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$dbname = getenv('DB_NAME');

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode([
        "success" => false,
        "message" => "Échec de la connexion à la base de données : " . $conn->connect_error
    ]));
}

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

$conn->query($createTableSQL);
