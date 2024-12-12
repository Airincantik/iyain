<?php
// Set the timezone to your local timezone
date_default_timezone_set('Asia/Jakarta');  // Adjust based on your location

$current_time = date("H:i");  // Get the current time in H:i format (24-hour format)

// Set the times for when the test registration opens and closes
$start_time = "07.00";
$end_time = "16:59";

// Check if the current time is between the start and end time
$is_test_open = strtotime($current_time) >= strtotime($start_time) && strtotime($current_time) < strtotime($end_time);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Registration</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f4;
            margin: 0;
        }

        .content {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .content h1 {
            font-size: 24px;
            color: #333;
        }

        .content p {
            font-size: 18px;
        }

        .register-button {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            text-decoration: none;
        }

        .register-button:hover {
            background-color: #0056b3;
        }

        .wait-message {
            color: red;
            font-size: 16px;
            margin-top: 20px;
        }

        .closed-message {
            color: red;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="content">
        <h1>Test Registration</h1>

        <?php if ($is_test_open): ?>
            <!-- Display the registration button if it's between 15:26 and 16:00 -->
            <p>Registrasi sekarang!</p>
            <a href="akses.php" class="register-button">Masukkan Kode Akses</a>

        <?php elseif (strtotime($current_time) < strtotime($start_time)): ?>
            <!-- Show a message to wait if it's before 15:26 -->
            <p>Test dimulai pukul 15:26</p>
            <p class="wait-message">Tunggu pukul 15:26 untuk registrasi</p>
        <?php else: ?>
            <!-- Show a message that registration is closed if it's after 16:00 -->
            <p>Pendaftaran ditutup</p>
            <p class="closed-message">Test dimulai pukul 15:26 hingga pukul 16:00</p>
        <?php endif; ?>
    </div>
</body>

</html>