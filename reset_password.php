<?php
require 'config.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Token inválido.");
}

$stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    die("Token inválido.");
}

$stmt->bind_result($email, $expires_at);
$stmt->fetch();

if (strtotime($expires_at) < time()) {
    die("Token expirado.");
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha - AgriApp</title>
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
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #4CAF50;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            margin-bottom: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2E7D32;
        }

        .footer {
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Redefinir Senha</h2>
        <form action="update_password.php" method="POST">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="password" name="new_password" placeholder="Nova Senha" required>
            <button type="submit">Salvar Nova Senha</button>
        </form>
        <div class="footer">
            © <?= date("Y") ?> AgriApp. Todos os direitos reservados.
        </div>
    </div>
</body>
</html>
