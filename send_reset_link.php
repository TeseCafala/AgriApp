<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    // Verifica se email existe em alguma das tabelas
    $found = false;
    $tables = ["consumer", "farmers_", "admin"];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SELECT id FROM $table WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $found = true;
            break;
        }
        $stmt->close();
    }

    if ($found) {
        $token = hash("sha256", uniqid());
        $expires = date("Y-m-d H:i:s", time() + 3600);

        $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE token=?, expires_at=?");
        $stmt->bind_param("sssss", $email, $token, $expires, $token, $expires);
        $stmt->execute();
        $stmt->close();

        $reset_link = "http://localhost/agriapp/reset_password.php?token=$token";

        // Mostra o link diretamente
        $msg = "Enviamos um link de redefinição para seu email.";
    } else {
        $msg = "Email não encontrado no sistema.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Link de Redefinição - AgriApp</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #4CAF50;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
        }
        h2 {
            color: #333;
        }
        .message {
            margin-top: 20px;
            color: #333;
            font-size: 16px;
        }
        .link {
            margin-top: 15px;
            background-color: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
            word-wrap: break-word;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Link de Redefinição</h2>
        <div class="message"><?= $msg ?? '' ?></div>
        <?php if (isset($reset_link)): ?>
            <div class="link">
                <a href="<?= $reset_link ?>"><?= $reset_link ?></a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
