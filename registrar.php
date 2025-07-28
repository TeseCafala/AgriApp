<?php
session_start();
require 'config.php'; // Ensure this path is correct for your database connection

if (isset($_SESSION['user_id'])) {
    echo "<script>alert('Precisa de fazer o logout para se registrar!'); window.history.back();</script>";
    exit();
}

if (isset($_POST["submit"])) {
    $name = trim($_POST["nome"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $phone = trim($_POST["telefone"]); // HTML input name is 'telefone', PHP variable maps to 'phone' column
    $role = $_POST["role"]; // 'consumer' or 'farmer'

    // --- Input Validation ---
    if (empty($name) || empty($email) || empty($password) || empty($phone) || empty($role)) {
        echo "<script>alert('Por favor, preencha todos os campos.');</script>";
        exit();
    }

    // You might want to add more robust password hashing here (e.g., password_hash)
    // For now, we'll keep it as plain text as in your login.php for consistency
    $hashed_password = $password; // In a real app, use password_hash($password, PASSWORD_DEFAULT);

    // --- Check if user already exists ---
    $check_table = ($role === 'consumer') ? 'consumer' : 'farmers_';
    $check_query = "SELECT id FROM $check_table WHERE email = ?";
    $stmt_check = mysqli_prepare($conn, $check_query);
    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, "s", $email);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            echo "<script>alert('Este email já está registrado. Por favor, faça login ou use outro email.');</script>";
            mysqli_stmt_close($stmt_check);
            exit();
        }
        mysqli_stmt_close($stmt_check);
    } else {
        die("Erro na preparação da consulta de verificação: " . mysqli_error($conn));
    }

    // --- Insert User based on Role ---
    $insert_success = false;
    // Variable to hold the empty string for required fields not in the form
    $default_empty_string = "";

    if ($role === 'consumer') {
        // Updated INSERT query and bind_param to use 'phone' column
        // Passing $default_empty_string by reference for delivery_address
        $insert_query = "INSERT INTO consumer (name, email, password, phone, delivery_address) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashed_password, $phone, $default_empty_string);
            if (mysqli_stmt_execute($stmt)) {
                $insert_success = true;
            } else {
                echo "<script>alert('Erro ao registrar como consumidor: " . mysqli_error($conn) . "');</script>";
            }
            mysqli_stmt_close($stmt);
        } else {
            die("Erro na preparação da consulta de consumidor: " . mysqli_error($conn));
        }
    } elseif ($role === 'farmer') {
        // Updated INSERT query and bind_param to use 'phone' column
        // Passing $default_empty_string by reference for bank_account
        $insert_query = "INSERT INTO farmers_ (name, email, password, phone, bank_account) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insert_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $hashed_password, $phone, $default_empty_string);
            if (mysqli_stmt_execute($stmt)) {
                $insert_success = true;
            } else {
                echo "<script>alert('Erro ao registrar como agricultor: " . mysqli_error($conn) . "');</script>";
            }
            mysqli_stmt_close($stmt);
        } else {
            die("Erro na preparação da consulta de agricultor: " . mysqli_error($conn));
        }
    }

    if ($insert_success) {
        echo "<script>alert('Registro bem-sucedido! Agora você pode fazer login.'); window.location.href = 'login.php';</script>";
    } else {
        echo "<script>alert('Falha no registro. Por favor, tente novamente.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Agriapp</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="registrar.css">
</head>
<body>
    <div class="container">
        <div class="right">
            <div class="lema">
                <h1>Junte-se à nossa comunidade Agriapp!</h1>
            </div>
        </div>

        <div class="left">
            <form action="registrar.php" method="POST">
                <div class="form-group">
                    <h1>Registrar</h1>

                    <div class="input-container">
                        <i class="fas fa-user"></i>
                        <input type="text" name="nome" placeholder="Nome Completo" required>
                    </div>

                    <div class="input-container">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Senha" required>
                    </div>

                    <div class="input-container">
                        <i class="fas fa-phone"></i> <input type="text" name="telefone" placeholder="Telefone" required>
                    </div>

                    <div class="role-selection">
                        <label>Registrar como:</label>
                        <div class="radio-group">
                            <input type="radio" id="consumer" name="role" value="consumer" required>
                            <label for="consumer">Cliente</label>

                            <input type="radio" id="farmer" name="role" value="farmer">
                            <label for="farmer">Agricultor</label>
                        </div>
                    </div>
                    <button type="submit" name="submit" class="button">Criar Conta</button>

                    <div class="terms">
                        <label>Já tem conta? <a href="login.php">Faça Login!</a></label>
                        <label>Ao se registrar, você concorda com nossos <a href="#" id="openTerms">Termos e Condições</a>.</label>
                    </div>

                     <div class="terms">
                        <label><a href="index.php">← Voltar à página inicial</a></label>
                    </div>  
                </div>
            </form>
        </div>
    </div>

    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Termos e Condições da Agriapp</h2>
            <p>Bem-vindo à Agriapp! Ao se registrar e utilizar nossos serviços, você concorda em cumprir e ser regido por estes Termos e Condições, nossa Política de Privacidade e todas as leis e regulamentos aplicáveis.</p>
            <h3>1. Aceitação dos Termos</h3>
            <p>Ao acessar ou usar a plataforma Agriapp, você concorda em cumprir e ser regido por estes Termos e Condições, nossa Política de Privacidade e todas as leis e regulamentos aplicáveis.</p>
            <h3>2. Cadastro e Contas</h3>
            <p>Para utilizar certos recursos da Agriapp, você deverá se registrar e criar uma conta. Você é responsável por manter a confidencialidade de suas informações de conta e por todas as atividades que ocorrem em sua conta.</p>
            <h3>3. Uso da Plataforma</h3>
            <p>A Agriapp é uma plataforma que conecta agricultores e consumidores. Você concorda em usar a plataforma apenas para fins legítimos e de acordo com estes termos.</p>
            <h3>4. Conduta do Usuário</h3>
            <p>Você concorda em não:</p>
            <ul>
                <li>Publicar conteúdo ilegal, difamatório, obsceno ou prejudicial.</li>
                <li>Violar quaisquer leis locais, estaduais, nacionais ou internacionais.</li>
                <li>Interferir ou interromper o funcionamento da plataforma ou dos servidores conectados.</li>
            </ul>
            <h3>5. Privacidade</h3>
            <p>Sua privacidade é muito importante para nós. Nossa Política de Privacidade detalha como coletamos, usamos e protegemos suas informações pessoais.</p>
            <h3>6. Alterações nos Termos</h3>
            <p>A Agriapp reserva-se o direito de modificar estes Termos e Condições a qualquer momento. Quaisquer alterações serão publicadas na plataforma e entrarão em vigor imediatamente.</p>
            <h3>7. Rescisão</h3>
            <p>Podemos rescindir ou suspender sua conta e acesso à plataforma imediatamente, sem aviso prévio, por qualquer motivo, incluindo, sem limitação, uma violação destes Termos e Condições.</p>
            <h3>8. Contato</h3>
            <p>Se você tiver alguma dúvida sobre estes Termos e Condições, entre em contato conosco.</p>
        </div>
    </div>

    <script>
        // JavaScript for Modal
        const termsModal = document.getElementById('termsModal');
        const openTermsBtn = document.getElementById('openTerms');
        const closeButton = document.querySelector('.close-button');

        openTermsBtn.addEventListener('click', function(event) {
            event.preventDefault(); // Prevent default link behavior
            termsModal.style.display = 'block';
        });

        closeButton.addEventListener('click', function() {
            termsModal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == termsModal) {
                termsModal.style.display = 'none';
            }
        });
    </script>
</body>
</html>