<?php
include 'config.php';
session_start();

// Set the timezone to your local timezone
date_default_timezone_set('Asia/Jakarta'); // Adjust based on your location

// Get current time
$current_time = date("H:i");

// Set the time when the registration ends
$end_time = "17:00";

// Redirect if the time is past the registration end time
if (strtotime($current_time) >= strtotime($end_time)) {
    header("Location: index.php");
    exit();
}

$error_message = '';  // Initialize an empty error message
$success_message = ''; // Initialize a success message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if access code is provided (for registration)
    if (isset($_POST['access_code']) && !empty($_POST['access_code'])) {
        $email = $_POST['email'];
        $password = md5($_POST['password']);
        $access_code = $_POST['access_code'];

        // Verify access code in database
        $stmt = $conn->prepare("SELECT id FROM access_codes WHERE code = ?");
        $stmt->bind_param("s", $access_code);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // If valid, register user
            $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $email, $password);
            $stmt->execute();
            $stmt->close();

            $success_message = "Registrasi berhasil! Anda akan diarahkan ke pedoman tes.";

            // Save user session and redirect to pedoman
            $_SESSION['akses_valid'] = true;
            $_SESSION['email'] = $email;

            // Redirect to pedoman-test.php in peserta folder
            header("Location: /hm/peserta/pedoman-test.php");
            exit();
        } else {
            $error_message = "Kode akses tidak valid!";
        }
    } else {
        // Handle login
        $email = $_POST['email'];
        $password = md5($_POST['password']);

        // Check if email and password match in database
        $stmt = $conn->prepare("SELECT id, name, role FROM users WHERE email = ? AND password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $name, $role);
            $stmt->fetch();

            // Save user session
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;

            // Redirect to pedoman-test.php if role is peserta
            if ($role == 'peserta') {
                header("Location: /hm/peserta/pedoman-test.php");
            } elseif ($role == 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($role == 'proktor') {
                header("Location: proktor_dashboard.php");
            } else {
                header("Location: peserta_dashboard.php");
            }
            exit();
        } else {
            $error_message = "Email atau password salah!";
        }
    }

    $stmt->close();
    $conn->close();
}
?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Styling for the form */
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
        }

        .login-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .login-container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .login-container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
            display: block;
            margin-top: 10px;
        }

        .login-container a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }

        .success-message {
            color: green;
            margin-top: 10px;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php if (empty($error_message) && isset($_POST['access_code'])): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <!-- Show access code field if not logging in -->
            <?php if (empty($error_message) && !isset($_POST['access_code'])): ?>
                <input type="text" name="access_code" placeholder="Masukkan Kode Akses" required>
            <?php endif; ?>

            <button type="submit">
                <?php echo isset($_POST['access_code']) ? 'Registrasi' : 'Login'; ?>
            </button>
        </form>

        <?php if (!empty($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <a href="register.php">Belum punya akun? Daftar di sini</a>
    </div>
</body>

</html>