<?php
require_once 'config.php'; // Koneksi ke database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $access_code = trim($_POST['access_code']);

    // Cek kode akses di database
    $sql = "SELECT * FROM access_codes WHERE access_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $access_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $test = $result->fetch_assoc();

        // Redirect ke halaman pedoman test sesuai mata pelajaran
        header("Location:/hm/peserta/pedoman-test.php?access_code=" . $access_code);
        exit;
    } else {
        $error_message = "Kode akses tidak valid.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masukkan Kode Akses</title>
</head>

<body>
    <h1>Masukkan Kode Akses</h1>
    <form action="akses.php" method="POST">
        <input type="text" name="access_code" placeholder="Masukkan Kode Akses" required>
        <button type="submit">Submit</button>
    </form>
    <?php if (isset($error_message)) { ?>
        <p style="color: red;"><?= $error_message; ?></p>
    <?php } ?>
</body>

</html>