<?php
require 'config.php';

if (isset($_GET['id'], $_GET['type'])) {
    $id = (int)$_GET['id'];
    $type = $_GET['type'];

    if ($type === 'cliente') {
        $table = 'consumer';
    } elseif ($type === 'agricultor') {
        $table = 'farmers_';
    } else {
        header("Location: admin_dashboard.php?page=users&error=Tipo de usuário inválido");
        exit;
    }

    $query = "UPDATE `$table` SET archived = 0 WHERE id = $id";

    if (mysqli_query($conn, $query)) {
        header("Location: admin_dashboard.php?page=users&success=Usuário restaurado com sucesso");
        exit;
    } else {
        header("Location: admin_dashboard.php?page=users&error=Erro ao restaurar usuário");
        exit;
    }
} else {
    header("Location: admin_dashboard.php?page=users&error=Parâmetros inválidos");
    exit;
}
