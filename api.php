<?php
header('Content-Type: application/json');
require 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $stmt = $pdo->query("SELECT * FROM tickets ORDER BY created_at DESC");
    $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Map SQL 'created_at' to JS 'createdAt'
    $formattedTickets = array_map(function($t) {
        $t['createdAt'] = $t['created_at'];
        unset($t['created_at']);
        return $t;
    }, $tickets);
    
    echo json_encode($formattedTickets);
    exit;
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("INSERT INTO tickets (id, title, description, category, priority, status, creator, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $id = $data['id']; 
    $createdAt = date('Y-m-d H:i:s', strtotime($data['createdAt'])); 

    try {
        $stmt->execute([
            $id,
            $data['title'],
            $data['description'],
            $data['category'],
            $data['priority'],
            $data['status'],
            $data['creator'],
            $createdAt
        ]);
        echo json_encode(["success" => true, "id" => $id]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

if ($method === 'PATCH') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing ticket ID"]);
        exit;
    }

    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
    $stmt->execute([$data['status'], $id]);
    
    echo json_encode(["success" => true]);
    exit;
}
?>