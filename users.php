<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); 
ini_set('session.cookie_samesite', 'Strict');
session_start();

header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access. Please log in."]);
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(["error" => "Forbidden: Only administrators can manage users."]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, name, email, role FROM users ORDER BY name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required user fields."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $hashedPassword = password_hash(trim($data['password']), PASSWORD_DEFAULT);
    
    try {
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            $hashedPassword, 
            $data['role']
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        if ($e->getCode() == 23000) { 
            echo json_encode(["error" => "A user with this email already exists."]);
        } else {
            echo json_encode(["error" => "Failed to create user."]);
        }
    }
    exit;
}

if ($method === 'PATCH') {
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$id || empty($data['name']) || empty($data['email']) || empty($data['role'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required fields or user ID"]);
        exit;
    }

    try {
        if (!empty($data['password'])) {
            $hashedPassword = password_hash(trim($data['password']), PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
            $stmt->execute([trim($data['name']), trim($data['email']), $hashedPassword, $data['role'], $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
            $stmt->execute([trim($data['name']), trim($data['email']), $data['role'], $id]);
        }
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        if ($e->getCode() == 23000) {
            echo json_encode(["error" => "Email address is already in use by another account."]);
        } else {
            echo json_encode(["error" => "Failed to update user."]);
        }
    }
    exit;
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing user ID"]);
        exit;
    }

    if ($id == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(["error" => "You cannot delete your own active account."]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to delete user."]);
    }
    exit;
}
?>
