<?php
require 'config.php';
$message = '';
$type = '';

if (isset($_GET['success'])) {
    $message = $_GET['success'];
    $type = 'success';
} elseif (isset($_GET['error'])) {
    $message = $_GET['error'];
    $type = 'error';
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Usuários - AgriApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #2e7d32;
            --secondary: #a5d6a7;
            --light: #f4f8f4;
            --text-dark: #333;
            --danger: #e53935;
            --success: #388e3c;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--light);
            color: var(--text-dark);
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
        }

        h2 {
            margin: 30px 0 10px;
            color: var(--primary);
            border-left: 6px solid var(--secondary);
            padding-left: 12px;
            font-size: 1.4rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
        }

        th {
            background-color: var(--secondary);
            color: var(--primary);
        }

        tr:nth-child(even) {
            background-color: #f1f8e9;
        }

        tr:hover {
            background-color: #dcedc8;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .btn-danger {
            background-color: var(--danger);
        }

        .btn-danger:hover {
            background-color: #c62828;
        }

        .btn-success {
            background-color: var(--success);
        }

        .btn-success:hover {
            background-color: #1b5e20;
        }

        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            td {
                position: relative;
                padding-left: 50%;
                border-bottom: 1px solid #ccc;
            }

            td::before {
                position: absolute;
                top: 12px;
                left: 15px;
                width: 45%;
                font-weight: bold;
                color: var(--primary);
                white-space: nowrap;
            }

            td:nth-of-type(1)::before { content: "ID"; }
            td:nth-of-type(2)::before { content: "Nome"; }
            td:nth-of-type(3)::before { content: "Email"; }
            td:nth-of-type(4)::before { content: "Telefone"; }
            td:nth-of-type(5)::before { content: "Ações"; }
        }

        .modal-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #fff;
            border-left: 6px solid;
            padding: 15px 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            border-radius: 6px;
            min-width: 300px;
            max-width: 500px;
            font-family: 'Segoe UI', sans-serif;
            animation: slideDown 0.5s ease;
        }

        .modal-message.success {
            border-color: #2e7d32;
            color: #2e7d32;
        }

        .modal-message.error {
            border-color: #c62828;
            color: #c62828;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translate(-50%, -20px);
            }
            to {
                opacity: 1;
                transform: translate(-50%, 0);
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Usuários - Clientes</h2>
    <?php
    $query_clients = "SELECT * FROM consumer ORDER BY id DESC";
    $result_clients = mysqli_query($conn, $query_clients);

    if ($result_clients && mysqli_num_rows($result_clients) > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = mysqli_fetch_assoc($result_clients)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>";

            if ($row['archived'] == 0) {
                echo "<a href='archive_user.php?id={$row['id']}&type=cliente' class='btn btn-danger' onclick=\"return confirm('Arquivar este cliente?');\">Arquivar</a>";
            } else {
                echo "<a href='restore_user.php?id={$row['id']}&type=cliente' class='btn btn-success' onclick=\"return confirm('Restaurar este cliente?');\">Restaurar</a>";
            }

            echo "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nenhum cliente encontrado.</p>";
    }
    ?>

    <h2>Usuários - Agricultores</h2>
    <?php
    $query_farmers = "SELECT * FROM farmers_ ORDER BY id DESC";
    $result_farmers = mysqli_query($conn, $query_farmers);

    if ($result_farmers && mysqli_num_rows($result_farmers) > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Telefone</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = mysqli_fetch_assoc($result_farmers)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['phone']}</td>
                    <td>";

            if ($row['archived'] == 0) {
                echo "<a href='archive_user.php?id={$row['id']}&type=agricultor' class='btn btn-danger' onclick=\"return confirm('Arquivar este agricultor e seus produtos?');\">Arquivar</a>";
            } else {
                echo "<a href='restore_user.php?id={$row['id']}&type=agricultor' class='btn btn-success' onclick=\"return confirm('Restaurar este agricultor e seus produtos?');\">Restaurar</a>";
            }

            echo "</td></tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nenhum agricultor encontrado.</p>";
    }
    ?>
</div>

<?php if (!empty($message)): ?>
<div id="modalMessage" class="modal-message <?= $type ?>">
    <div class="modal-content">
        <p><?= htmlspecialchars($message) ?></p>
    </div>
</div>
<?php endif; ?>

<script>
    window.onload = () => {
        const modal = document.getElementById('modalMessage');
        if (modal) {
            modal.style.display = 'block';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 3000);
        }
    };
</script>
</body>
</html>
