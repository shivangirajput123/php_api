<?php

// Allow cross-origin requests
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}

// Parse incoming JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($data === null || !isset($data['basket'])) {
    // JSON decoding failed or 'basket' key is missing
    http_response_code(400);
    echo json_encode([
        "valid" => false,
        "status" => "JSON Decoding Error",
        "result" => [
            "message" => "Invalid JSON data or 'basket' key missing",
        ]
    ]);
    exit;
}

// Retrieve the 'basket' data from the decoded JSON
$basket = $data['basket'];

$bookPrices = [
    'book1' => 8,
    'book2' => 8,
    'book3' => 8,
    'book4' => 8,
    'book5' => 8
];

$discounts = [
    0,    // 0% discount for 0 different books
    0,    // 0% discount for 1 different book
    0.05, // 5% discount for 2 different books
    0.10, // 10% discount for 3 different books
    0.20, // 20% discount for 4 different books
    0.25  // 25% discount for all 5 different books
];

$totalPrice = calculateTotalPrice($basket, $bookPrices, $discounts);

// Send response
http_response_code(200);
echo json_encode([
    "valid" => true,
    "status" => 'OK',
    "result" => [
        "data" => $totalPrice,
    ]
]);

// Function to calculate total price
function calculateTotalPrice($basket, $bookPrices, $discounts)
{
    $bookCounts = array_count_values($basket);
    $totalPrice = 0;

    // Calculate the total number of different books in the basket
    $differentBooks = count($bookCounts);

    foreach ($bookCounts as $book => $count) {
        $totalPrice += $count * $bookPrices[$book] * (1 - $discounts[$differentBooks]);
    }

    return $totalPrice;
}
