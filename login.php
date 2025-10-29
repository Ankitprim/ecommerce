<?php
include __DIR__ . '/partials/header.php';

// If user is already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Check for a redirect URL
$redirect_url = 'index.php';
if (isset($_GET['redirect']) && $_GET['redirect'] == 'checkout') {
    $redirect_url = 'checkout.php';
}
?>

<style>
    .auth-container {
        max-width: 500px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .auth-tabs {
        display: flex;
        margin-bottom: 20px;
    }
    .auth-tab {
        flex: 1;
        text-align: center;
        padding: 15px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        color: #7f8c8d;
        border-bottom: 2px solid #ecf0f1;
    }
    .auth-tab.active {
        color: #e74c3c;
        border-bottom-color: #e74c3c;
    }
    .auth-form {
        display: none;
    }
    .auth-form.active {
        display: block;
    }
    .auth-form button {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        background: #e74c3c;
        color: white;
    }
    .auth-form .form-group {
        margin-bottom: 20px;
    }
    .auth-form .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }
    .auth-form .form-group input {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    #auth-message {
        margin-bottom: 15px;
        padding: 10px;
        border-radius: 4px;
        display: none;
    }
    #auth-message.success {
        background: #e8f4fc;
        color: #2980b9;
    }
    #auth-message.error {
        background: #fbeae_commerce-sitea;
        color: #c0392b;
    }
</style>

<div class="auth-container">
    <div class="auth-tabs">
        <div class="auth-tab active" data-form="login">Login</div>
        <div class="auth-tab" data-form="register">Register</div>
    </div>

    <input type="hidden" id="redirect-url" value="<?php echo $redirect_url; ?>">
    <div id="auth-message"></div>

    <form id="login-form" class="auth-form active">
        <input type="hidden" name="action" value="login">
        <div class="form-group">
            <label for="login-email">Email</label>
            <input type="email" id="login-email" name="email" required>
        </div>
        <div class="form-group">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>

    <form id="register-form" class="auth-form">
        <input type="hidden" name="action" value="register">
        <div class="form-group">
            <label for="register-name">Full Name</label>
            <input type="text" id="register-name" name="name" required>
        </div>
        <div class="form-group">
            <label for="register-email">Email</label>
            <input type="email" id="register-email" name="email" required>
        </div>
        <div class="form-group">
            <label for="register-password">Password</label>
            <input type="password" id="register-password" name="password" required>
        </div>
        <button type="submit">Register</button>
    </form>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>