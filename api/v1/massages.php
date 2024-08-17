<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle OPTIONS request for CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}
    
// Database connection settings
$host = 'localhost';
$db   = 'messages_db';
$user = 'messenger';
$pass = 'Bowen';

// Set up the DSN (Data Source Name)
$dsn = "pgsql:host=$host;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create a PDO instance (connect to the database)
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET TIME ZONE 'Europe/Amsterdam'");
    echo json_encode(['status' => 'success', 'message' => 'connected Database']);
} catch (\PDOException $e) {
    // If there is an error, stop execution and show the error
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if data is not empty
    if (isset($data['message']) && !empty($data['message'])) {
        $message = $data['message'];

        // Prepare and execute the SQL INSERT statement
        $sql = 'INSERT INTO messages (message) VALUES (:message)';
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute(['message' => $message]);
            // Respond with a success message
            echo json_encode(['status' => 'success', 'message' => 'Message stored successfully']);
        } catch (\PDOException $e) {
            // If there is an error with the SQL statement
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()]);
        }
    } else {
        // If the message is missing or empty, return an error
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No message provided']);
    }
} else {
    // If the request method is not POST, return a method not allowed error
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
}