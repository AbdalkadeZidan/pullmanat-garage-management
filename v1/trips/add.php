<?php

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Only POST method is allowed"
    ]);
    exit;
}

$requiredFields = [
    'departure_city',
    'destination_city',
    'trip_date',
    'trip_time',
    'trip_price',
    'bus_namber',
    'national_id'
];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || $_POST[$field] === '') {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "$field is required"
        ]);
        exit;
    }
}

$departure_city = $_POST['departure_city'];
$destination_city = $_POST['destination_city'];
$trip_date = $_POST['trip_date'];
$trip_time = $_POST['trip_time'];
$trip_price = $_POST['trip_price'];
$bus_namber = $_POST['bus_namber'];
$national_id = $_POST['national_id'];

include "../config/connected.php";
try {

    $stmt = $pdo->prepare("
        INSERT INTO trips (
            departure_city,
            destination_city,
            trip_date,
            trip_time,
            trip_price,
            bus_namber,
            national_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $departure_city,
        $destination_city,
        $trip_date,
        $trip_time,
        $trip_price,
        $bus_namber,
        $national_id
    ]);

    echo json_encode([
        "status" => "success",
        "message" => "Trip added successfully",
        "trip_id" => $pdo->lastInsertId()
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database error"
    ]);
}