<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0);
ini_set('session.cookie_samesite', 'Strict');
session_start();

header('Content-Type: application/json');

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing email or password."]);
    exit;
}

require 'db.php';

$stmt = $pdo->prepare("SELECT id, email, role, name, password FROM users WHERE email = ?");
$stmt->execute([$data['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($data['password'], $user['password'])) {
    
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['logged_in'] = true;
    
    unset($user['password']);
    
    echo json_encode(["success" => true, "user" => $user]);
} else {
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password."]);
}
?>
