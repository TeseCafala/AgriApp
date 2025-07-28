<?php
session_start();
include('config.php');

if (!isset($_SESSION['consumer_id']) && !isset($_SESSION['farmer_id'])) {
    echo "Você precisa estar logado para enviar mensagens.";
    exit;
}

$sender_id = isset($_SESSION['consumer_id']) ? $_SESSION['consumer_id'] : $_SESSION['farmer_id'];

if (isset($_POST['message']) && isset($_POST['receiver_id'])) {
    $mensagem = trim($_POST['message']);
    $receiver_id = intval($_POST['receiver_id']);

    $stmt = $mysqli->prepare("INSERT INTO mensagens (remetente_id, destinatario_id, mensagem) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $mensagem);

    if ($stmt->execute()) {
        echo "Mensagem enviada com sucesso!";
    } else {
        echo "Erro ao enviar mensagem: " . $stmt->error;
    }
} else {
    echo "Mensagem ou destinatário não especificado.";
}
