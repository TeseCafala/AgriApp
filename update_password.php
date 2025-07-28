<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $new_password = trim($_POST["new_password"]);

    if (empty($token) || empty($new_password)) {
        die("Token ou nova senha inválidos.");
    }

    // Verifica se o token existe e ainda é válido
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

    // Atualiza senha nas três tabelas possíveis
    $tables = ["consumer", "farmers_", "admin"];
    $updated = false;
    foreach ($tables as $table) {
        $stmt = $conn->prepare("UPDATE $table SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $new_password, $email);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $updated = true;
            break;
        }
        $stmt->close();
    }

    if ($updated) {
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();

        echo "<script>alert('Senha redefinida com sucesso!'); window.location.href='login.php';</script>";
        exit();
    } else {
        echo "<script>alert('Erro ao atualizar a senha.'); window.location.href='forgot_password.php';</script>";
        exit();
    }
}
?>
