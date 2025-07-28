<?php
session_start();
require 'config.php';

// Impede login se já estiver autenticado
if (isset($_SESSION['user_id'])) {
    echo "<script>alert('Precisa de fazer o logout!'); window.history.back();</script>";
    exit();
}

$loginSuccess = false;
$redirectTo = "";
$errorMessage = null;

// Função de verificação de usuário
function checkUser($conn, $table, $name, $password, $role, $redirect)
{
    global $loginSuccess, $redirectTo, $errorMessage;

    $query = "SELECT * FROM $table WHERE name = ?";
    $stmt = mysqli_prepare($conn, $query);
    if (!$stmt) {
        die("Erro: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (isset($row['archived']) && $row['archived'] == 1) {
            $errorMessage = "Usuário arquivado. Acesso negado.";
            return false;
        }

        if ($password === $row['password']) {
            $_SESSION['user_id'] = $_SESSION['id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role'] = $role;

            if ($role === "farmer") {
                $_SESSION['farmer_id'] = $row['id'];
            }

            if ($role === "admin") {
                $_SESSION['admin_logged_in'] = true;
            }

            $loginSuccess = true;
            $redirectTo = $redirect;
            return true;
        } else {
            $errorMessage = "Senha incorreta.";
            return false;
        }
    }

    return false;
}

// Quando o formulário é submetido
if (isset($_POST["submit"])) {
    $name = trim($_POST["nome"]);
    $password = trim($_POST["password"]);

    if (empty($name) || empty($password)) {
        $errorMessage = "Por favor, preencha todos os campos.";
    } else {
        $userFound =
            checkUser($conn, "admin", $name, $password, "admin", "admin/admin_dashboard.php") ||
            checkUser($conn, "consumer", $name, $password, "consumer", "cliente/produtos.php") ||
            checkUser($conn, "farmers_", $name, $password, "farmer", "agricultor/agricultor-dashboard.php");

        if (!$userFound && !$errorMessage) {
            $errorMessage = "Usuário não encontrado.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Login - AgriApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="right">
            <div class="lema">
                <h1>Revolucionando o setor agrícola do país.</h1>
            </div>
        </div>

        <div class="left">
            <form action="login.php" method="POST">
                <div class="form-group">
                    <h1>AgriApp</h1>

                    <div class="input-container">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nome" placeholder="Nome" required>
                    </div>

                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Senha" required>
                    </div>

                    <button type="submit" name="submit" class="button">Entrar</button>

                    <div class="terms">
                        <label>Ainda não tem conta? <a href="registrar.php">Registre-se!</a></label>
                    </div>

                    <div class="terms">
                        <label><a href="forgot_password.php">Esqueceu sua senha?</a></label>
                    </div>

                    <div class="terms">
                        <label><a href="index.php">← Voltar à página inicial</a></label>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de sucesso -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <i class="fas fa-check-circle"></i>
            <p id="modalText">Login bem-sucedido!</p>
        </div>
    </div>

    <!-- Toast de erro -->
    <div id="toast"></div>

    <script>
        function showModal(message, redirectUrl) {
            const modal = document.getElementById('successModal');
            const modalText = document.getElementById('modalText');
            modalText.textContent = message;
            modal.style.display = 'flex';
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, 2000);
        }

        function showToast(message) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        <?php if (!empty($errorMessage)) : ?>
        window.onload = () => showToast("<?= $errorMessage ?>");
        <?php endif; ?>

        <?php if ($loginSuccess) : ?>
        window.onload = () => showModal("Login bem-sucedido! Redirecionando...", "<?= $redirectTo ?>");
        <?php endif; ?>
    </script>
</body>
</html>
