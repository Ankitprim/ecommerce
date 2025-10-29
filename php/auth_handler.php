<?php
include 'db_connect.php'; // This also starts the session

header('Content-Type: application/json');
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

if (isset($_POST['action'])) {
    
    // --- REGISTER ACTION ---
    if ($_POST['action'] == 'register') {
        if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password'])) {
            $response['message'] = 'Please fill in all fields.';
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $response['message'] = 'Invalid email format.';
        } else {
            try {
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$_POST['email']]);
                if ($stmt->fetch()) {
                    $response['message'] = 'Email already registered.';
                } else {
                    // Create new user
                    $password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$_POST['name'], $_POST['email'], $password_hash]);
                    
                    $response['status'] = 'success';
                    $response['message'] = 'Registration successful! Please log in.';
                }
            } catch (PDOException $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
        }
    }
    
    // --- LOGIN ACTION ---
    if ($_POST['action'] == 'login') {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $response['message'] = 'Please fill in all fields.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$_POST['email']]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($_POST['password'], $user['password'])) {
                    // Password is correct!
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    
                    $response['status'] = 'success';
                    $response['message'] = 'Login successful!';
                    // Send redirect URL back to JS
                    $response['redirect'] = $_POST['redirect_url'] ?? 'index.php'; 
                } else {
                    $response['message'] = 'Invalid email or password.';
                }
            } catch (PDOException $e) {
                $response['message'] = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

echo json_encode($response);
?>