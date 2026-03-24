<?php
header('Content-Type: application/json');
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

// GET: Fetch all users
if ($method === 'GET') {
    $stmt = $pdo->query("SELECT id, name, email, role FROM users ORDER BY name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
    exit;
}

// POST: Create a new user
if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    try {
        $stmt->execute([
            trim($data['name']),
            trim($data['email']),
            trim($data['password']), // Still using plain-text based on our setup!
            $data['role']
        ]);
        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

// PATCH: Update an existing user
if ($method === 'PATCH') {
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing user ID"]);
        exit;
    }

    // If password is provided, update it. Otherwise, leave it alone.
    if (!empty($data['password'])) {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, password = ?, role = ? WHERE id = ?");
        $stmt->execute([trim($data['name']), trim($data['email']), trim($data['password']), $data['role'], $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([trim($data['name']), trim($data['email']), $data['role'], $id]);
    }
    
    echo json_encode(["success" => true]);
    exit;
}

// DELETE: Remove a user
if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing user ID"]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["success" => true]);
    exit;
}
?>