<?php
header('Content-Type: application/json');
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = trim($data['email'] ?? ''); // trim removes accidental spaces
$password = trim($data['password'] ?? '');

// Match plain text directly
$stmt = $pdo->prepare("SELECT email, role, name FROM users WHERE email = ? AND password = ?");
$stmt->execute([$email, $password]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    echo json_encode([
        "success" => true, 
        "user" => $user
    ]);
} else {
    // This sends a 401 status which triggers your 'alert' in the HTML
    http_response_code(401);
    echo json_encode(["error" => "Invalid email or password"]);
}
?>