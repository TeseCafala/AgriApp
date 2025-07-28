<?php
require 'config.php';

// Buscar todos usuários (agricultores + consumidores)
$agricultores = mysqli_query($conn, "SELECT id, name FROM farmers_ WHERE archived = 0");
$consumidores = mysqli_query($conn, "SELECT id, name FROM consumer");

// Envio de nova mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $destinatario_id = $_POST['destinatario_id'];
    $mensagem = mysqli_real_escape_string($conn, $_POST['mensagem']);
    $tipo_destinatario = $_POST['tipo_destinatario'];

    if (!empty($mensagem)) {
        $query = "INSERT INTO mensagens (remetente_id, destinatario_id, tipo_remetente, mensagem, status, data_envio)
                  VALUES (0, '$destinatario_id', 'admin', '$mensagem', 'nao_lida', NOW())";
        mysqli_query($conn, $query);
    }
}

// Buscar mensagens com destinatário selecionado
$mensagens = [];
if (isset($_GET['destinatario_id']) && isset($_GET['tipo'])) {
    $destinatario_id = $_GET['destinatario_id'];
    $tipo = $_GET['tipo'];

    $mensagens_query = "
        SELECT m.*, 
               CASE 
                   WHEN m.tipo_remetente = 'admin' THEN 'Admin'
                   WHEN m.tipo_remetente = 'agricultor' THEN (SELECT name FROM farmers_ WHERE id = m.remetente_id)
                   WHEN m.tipo_remetente = 'cliente' THEN (SELECT name FROM consumer WHERE id = m.remetente_id)
               END AS remetente_nome
        FROM mensagens m
        WHERE 
            (m.remetente_id = 0 AND m.destinatario_id = $destinatario_id AND m.tipo_remetente = 'admin')
            OR 
            (m.remetente_id = $destinatario_id AND m.tipo_remetente = '$tipo' AND m.destinatario_id = 0)
        ORDER BY m.data_envio ASC
    ";

    $mensagens = mysqli_query($conn, $mensagens_query);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Chat com Usuário - Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }

        select, textarea, input[type="submit"] {
            margin-top: 10px;
            padding: 10px;
            font-size: 14px;
        }

        .mensagens {
            background: white;
            border-radius: 8px;
            padding: 15px;
            height: 300px;
            overflow-y: scroll;
            border: 1px solid #ccc;
        }

        .msg {
            margin-bottom: 10px;
        }

        .msg.admin {
            text-align: right;
            color: green;
        }

        .msg.outro {
            text-align: left;
            color: #333;
        }

        .chat-form {
            margin-top: 20px;
        }

        .chat-form textarea {
            width: 100%;
            height: 70px;
        }
    </style>
</head>
<body>
    <h2>Mini Chat Admin</h2>

    <form method="get">
        <label>Escolher usuário:</label><br>
        <select name="destinatario_id" required>
            <option value="">-- Selecione --</option>
            <optgroup label="Agricultores">
                <?php while ($row = mysqli_fetch_assoc($agricultores)): ?>
                    <option value="<?= $row['id'] ?>" <?= (isset($_GET['destinatario_id']) && $_GET['destinatario_id'] == $row['id']) ? 'selected' : '' ?>>
                        Agricultor: <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </optgroup>
            <optgroup label="Consumidores">
                <?php while ($row = mysqli_fetch_assoc($consumidores)): ?>
                    <option value="<?= $row['id'] ?>" <?= (isset($_GET['destinatario_id']) && $_GET['destinatario_id'] == $row['id']) ? 'selected' : '' ?>>
                        Cliente: <?= htmlspecialchars($row['name']) ?>
                    </option>
                <?php endwhile; ?>
            </optgroup>
        </select>

        <select name="tipo" required>
            <option value="agricultor" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'agricultor') ? 'selected' : '' ?>>Agricultor</option>
            <option value="cliente" <?= (isset($_GET['tipo']) && $_GET['tipo'] == 'cliente') ? 'selected' : '' ?>>Consumidor</option>
        </select>

        <input type="submit" value="Abrir Chat">
    </form>

    <?php if (!empty($mensagens)): ?>
        <div class="mensagens">
            <?php while ($msg = mysqli_fetch_assoc($mensagens)): ?>
                <div class="msg <?= $msg['tipo_remetente'] === 'admin' ? 'admin' : 'outro' ?>">
                    <strong><?= htmlspecialchars($msg['remetente_nome']) ?>:</strong>
                    <?= htmlspecialchars($msg['mensagem']) ?>
                    <br><small><?= $msg['data_envio'] ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <form method="post" class="chat-form">
            <input type="hidden" name="destinatario_id" value="<?= $_GET['destinatario_id'] ?>">
            <input type="hidden" name="tipo_destinatario" value="<?= $_GET['tipo'] ?>">
            <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>
            <input type="submit" value="Enviar">
        </form>
    <?php elseif (isset($_GET['destinatario_id'])): ?>
        <p>Nenhuma mensagem ainda. Envie a primeira!</p>
        <form method="post" class="chat-form">
            <input type="hidden" name="destinatario_id" value="<?= $_GET['destinatario_id'] ?>">
            <input type="hidden" name="tipo_destinatario" value="<?= $_GET['tipo'] ?>">
            <textarea name="mensagem" placeholder="Digite sua mensagem..." required></textarea>
            <input type="submit" value="Enviar">
        </form>
    <?php endif; ?>
</body>
</html>
