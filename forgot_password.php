<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Senha - AgriApp</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

        :root {
            --primary-color: #4CAF50;
            --secondary-color: #2E7D32;
            --white: #fff;
            --gray: #f5f5f5;
            --text-dark: #333;
            --border-radius: 12px;
            --transition: 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--primary-color);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background-color: var(--white);
            padding: 40px 30px;
            border-radius: var(--border-radius);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }

        h2 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 24px;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px 14px;
            margin: 14px 0 20px;
            border: 1px solid #ccc;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border-color var(--transition), box-shadow var(--transition);
        }

        input[type="email"]:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.2);
            outline: none;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color var(--transition), transform var(--transition);
        }

        button:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .message {
            margin-top: 18px;
            font-size: 0.95rem;
            color: var(--text-dark);
        }

        .back-link {
            margin-top: 20px;
        }

        .back-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.95rem;
        }

        .back-link a:hover {
            text-decoration: underline;
            color: var(--secondary-color);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Recuperar Senha</h2>
        <form action="send_reset_link.php" method="POST">
            <input type="email" name="email" placeholder="Digite seu email" required>
            <button type="submit">Enviar link de redefinição</button>
        </form>
        <div class="message">Você receberá o link logo abaixo.</div>
        <div class="back-link">
            <a href="login.php">← Voltar à página inicial</a>
        </div>
    </div>
</body>
</html>
