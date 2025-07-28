<?php
require '../config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php?page=products&error=ID inválido.");
    exit;
}

$product_id = intval($_GET['id']);

$stmt = $conn->prepare("UPDATE productsz SET ativo = 0 WHERE id = ?");
$stmt->bind_param('i', $product_id);

if ($stmt->execute()) {
    header("Location: admin_dashboard.php?page=products&success=Produto arquivado.");
} else {
    header("Location: admin_dashboard.php?page=products&error=Erro ao arquivar produto.");
}

$stmt->close();
$conn->close();
?>
