<?php
// CORS headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Behandle OPTIONS verzoek voor CORS preflight
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
    // Create a PDO instance (connect database)
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET TIME ZONE 'Europe/Amsterdam'");
} catch (\PDOException $e) {
    // Error bericht
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Behandel POST verzoek om bericht toe te voegen.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Controle voor lege bericht
    if (isset($data['message']) && !empty($data['message'])) {
        $message = $data['message'];

        // Execute de SQL INSERT statement
        $sql = 'INSERT INTO messages (message) VALUES (:message)';
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute(['message' => $message]);
            // Bericht approved
            echo json_encode(['status' => 'success', 'message' => 'Message stored successfully']);
        } catch (\PDOException $e) {
            // Fail van SQL
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database operation failed: ' . $e->getMessage()]);
        }
    } else {
        // Error geen bericht
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'No message provided']);
    }
} 

// Berichtgegevens voor grafiek
elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Berichtgegevens op dagen
    $sql = "
        SELECT
            DATE(created_at) AS date,
            COUNT(*) AS message_count
        FROM messages
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at) DESC
    ";
    $stmt = $pdo->query($sql);

    // Controle op data
    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll();
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
}

