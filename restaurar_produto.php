<?php
require '../config.php';

// Verificar se o ID do produto foi passado corretamente
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_dashboard.php?page=products&error=ID inválido.");
    exit;
}

$product_id = intval($_GET['id']); // Pega o ID do produto para restaurar

// Atualiza o produto para "ativo" (1)
$stmt = $conn->prepare("UPDATE productsz SET ativo = 1 WHERE id = ?");
$stmt->bind_param('i', $product_id);

// Executa a consulta
if ($stmt->execute()) {
    // Redireciona de volta para a lista de produtos com sucesso
    header("Location: admin_dashboard.php?page=products&success=Produto restaurado.");
} else {
    // Se houver erro, exibe uma mensagem de erro
    header("Location: admin_dashboard.php?page=products&error=Erro ao restaurar produto.");
}

// Fecha a conexão
$stmt->close();
$conn->close();
?>
