<?php
// session_start();
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ? AND user_type = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            header("Location: dashboard.php");
            exit();
        }
    }
    
    $error = "Invalid admin credentials!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Lawyer System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="admin-login">
    <div class="login-container">
        <div class="login-form">
            <h1>Admin Login</h1>
            <form method="POST">
                <?php if(isset($error)) echo '<div class="alert-error">'.$error.'</div>'; ?>
                <input type="email" name="email" placeholder="Admin Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" class="btn-primary">Login as Admin</button>
            </form>
        </div>
    </div>
</body>
</html>

