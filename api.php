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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $sql = "SELECT tickets.*, users.name AS linked_name 
            FROM tickets 
            LEFT JOIN users ON tickets.creator = CAST(users.id AS CHAR)
            ORDER BY created_at DESC";
            
    try {
        $stmt = $pdo->query($sql);
        $tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        
        $formattedTickets = array_map(function($t) use ($isAdmin) {
            $t['createdAt'] = $t['created_at'];
            unset($t['created_at']);
            
            if (!empty($t['linked_name'])) {
                $t['creator'] = $t['linked_name'];
            }
            unset($t['linked_name']); 
            
            // SECURITY GATE: Only admins see private notes
            if (!$isAdmin) {
                unset($t['admin_notes']); 
            }
            
            return $t;
        }, $tickets);
        
        echo json_encode($formattedTickets);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch tickets: " . $e->getMessage()]);
    }
    exit;
}


if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Basic validation
    if (empty($data['title']) || empty($data['description']) || empty($data['category'])) {
        http_response_code(400);
        echo json_encode(["error" => "Missing required ticket fields."]);
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO tickets (id, title, description, category, priority, status, creator, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
   
    $id = 'TKT-' . strtoupper(substr(uniqid(), -6)); 
    $createdAt = date('Y-m-d H:i:s'); 
    
  
    $creatorToStore = $_SESSION['user_name'] ?? $data['creator'] ?? 'Unknown User'; 

    try {
        $stmt->execute([
            $id,
            trim($data['title']),
            trim($data['description']),
            $data['category'],
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'open',
            $creatorToStore, 
            $createdAt
        ]);
        echo json_encode(["success" => true, "id" => $id]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Database error occurred."]);
    }
    exit;
}

if ($method === 'PATCH') {
    $id = $_GET['id'] ?? null;
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "Missing ticket ID"]);
        exit;
    }
    
    // Admin check
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        http_response_code(403);
        echo json_encode(["error" => "Forbidden: Only admins can update tickets."]);
        exit;
    }

    try {
        if (isset($data['status'])) {
            $stmt = $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?");
            $stmt->execute([$data['status'], $id]);
        } 
        
        if (isset($data['admin_notes'])) {
            $stmt = $pdo->prepare("UPDATE tickets SET admin_notes = ? WHERE id = ?");
            $stmt->execute([trim($data['admin_notes']), $id]);
        }

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to update ticket."]);
    }
    exit;
}
?>
