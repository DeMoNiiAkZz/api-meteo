<?php
header('Content-Type: application/json');

// Exemple d'alerte maritime
$alerteMaritime = [
    "alerte" => "Vague de tempête",
    "niveau" => "Rouge",
    "description" => "Des vents violents et des vagues importantes sont attendus.",
    "zone" => "Côte Normande",
    "debut" => "2023-10-01 14:00:00",
    "fin" => "2023-10-02 18:00:00"
];

// Restituer le JSON
echo json_encode($alerteMaritime, JSON_PRETTY_PRINT);
?>