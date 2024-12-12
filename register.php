<?php
require_once 'config.php'; // Koneksi ke database

if (!isset($_GET['access_code'])) {
    header("Location: akses.php"); // Kembali ke halaman sebelumnya jika tidak ada kode akses
    exit;
}

$access_code = $_GET['access_code'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $nomor_hp = $_POST['phone_number']; // Sesuaikan dengan kolom di database
    $email = $_POST['email'];
    $jenis_kelamin = $_POST['gender']; // Sesuaikan dengan kolom di database
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    if (empty($name) || empty($nomor_hp) || empty($email) || empty($jenis_kelamin) || empty($password)) {
        $error_message = "Semua field harus diisi.";
    } else {
        $sql_check = "SELECT * FROM users WHERE email = ?";
        $stmt_check = $conn->prepare($sql_check);
        if ($stmt_check === false) {
            die('Query preparation failed: ' . $conn->error);
        }

        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $error_message = "Email sudah terdaftar.";
        } else {
            $sql = "INSERT INTO users (name, nomor_hp, email, jenis_kelamin, password) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die('Query preparation failed: ' . $conn->error);
            }

            $stmt->bind_param("sssss", $name, $nomor_hp, $email, $jenis_kelamin, $password);

            if ($stmt->execute()) {
                header("Location: ../hm/peserta/pedoman-test.php?access_code=" . $access_code);
                exit;
            } else {
                $error_message = "Gagal menyimpan data. Coba lagi.";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Pengguna</title>
</head>

<body>
    <h1>Registrasi Pengguna</h1>
    <form action="register.php?access_code=<?= $access_code ?>" method="POST">
        <label for="name">Nama Lengkap:</label>
        <input type="text" id="name" name="name" placeholder="Nama Lengkap" required><br>

        <label for="phone_number">Nomor HP:</label>
        <input type="text" id="phone_number" name="phone_number" placeholder="Nomor HP" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Email" required><br>

        <label for="gender">Jenis Kelamin:</label>
        <select id="gender" name="gender" required>
            <option value="L">Laki-Laki</option>
            <option value="P">Perempuan</option>
        </select><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" placeholder="Password" required><br>

        <button type="submit">Daftar</button>
    </form>

    <?php if (isset($error_message)) { ?>
        <p style="color: red;"><?= $error_message; ?></p>
    <?php } ?>
</body>

</html>