<?php
require __DIR__ . '/vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'Cors.php';
include 'db.php'; 
include 'validate.php'; 

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the Authorization header
    $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (empty($authHeader)) {
        $headers = apache_request_headers();
        $authHeader = $headers['Authorization'] ?? '';
    }

    if (empty($authHeader)) {
        echo json_encode(['success' => false, 'error' => 'Authorization token missing or malformed']);
        exit;
    }

    if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        echo json_encode(['success' => false, 'error' => 'Authorization token missing']);
        exit;
    }

    $jwt = $matches[1]; 

    $userId = validateJWT($jwt);

    if (!$userId) {
        echo json_encode(['success' => false, 'error' => 'Invalid or expired token']);
        exit;
    }

    // Get the POST data
    $postData = json_decode(file_get_contents('php://input'), true);
    if (empty($postData)) {
        echo json_encode(['success' => false, 'error' => 'Invalid input data']);
        exit;
    }

    // Validate class name and capacity
    $className = trim($postData['class_name'] ?? '');
    $capacity = trim($postData['capacity'] ?? '');

    if (empty($className) || !is_numeric($capacity) || $capacity <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid class name or capacity']);
        exit;
    }

    // Generate a unique class code
    $classCode = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

    try {
        // Prepare SQL query to insert the class data
        $stmt = $pdo->prepare("INSERT INTO classes (class_name, capacity, created_by, class_code) VALUES (?, ?, ?, ?)");
        $stmt->execute([$className, $capacity, $userId, $classCode]);

        // Check if the insert was successful by checking the last insert ID
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'class_code' => $classCode]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to insert class into the database']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}

$conn = null;
?>
