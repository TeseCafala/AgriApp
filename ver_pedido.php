<?php
// ver_pedido.php
require 'config.php';

if (isset($_GET['id'])) {
    $order_id = intval($_GET['id']);

    $query = "
        SELECT 
            p.order_id,
            p.user_id,
            p.farmer_id,
            p.order_date,
            p.product_name,
            p.total_value,
            p.payment_method,
            p.status,
            p.item_count,
            u.name AS consumer_name,
            f.name AS farmer_name
        FROM pedidos_ p
        LEFT JOIN consumer u ON p.user_id = u.id
        LEFT JOIN farmers_ f ON p.farmer_id = f.id
        WHERE p.order_id = $order_id
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $order = mysqli_fetch_assoc($result);
    } else {
        echo "<script>alert('Pedido não encontrado!'); window.location.href = 'admin_orders.php';</script>";
        exit();
    }

    $query_items = "
        SELECT 
            oi.order_item_id,
            oi.product_id,
            oi.quantity,
            oi.price_at_order,
            p.name AS product_name,
            p.description AS product_description
        FROM order_items oi
        LEFT JOIN productsz p ON oi.product_id = p.id
        WHERE oi.order_id = $order_id
    ";

    $result_items = mysqli_query($conn, $query_items);

    $items = [];
    if ($result_items && mysqli_num_rows($result_items) > 0) {
        while ($item = mysqli_fetch_assoc($result_items)) {
            $items[] = $item;
        }
    }
} else {
    echo "<script>alert('ID de pedido inválido.'); window.location.href = 'admin_orders.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Pedido - AgriApp</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f9f6;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        header {
            text-align: center;
            margin-bottom: 30px;
        }

        header .brand {
            text-decoration: none;
            font-size: 1.8rem;
            color: #2e7d32;
            font-weight: bold;
            letter-spacing: 0.5px;
        }

        h1, h2, h3 {
            color: #2e7d32;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .order-details {
            background-color: #eaf6ef;
            border-left: 5px solid #2e7d32;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
        }

        .order-details p {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .order-details strong {
            color: #2e7d32;
        }

        .order-items table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 15px;
        }

        .order-items th {
            background-color: #2e7d32;
            color: white;
            padding: 14px;
            text-align: left;
            font-weight: 600;
            border-top-left-radius: 6px;
            border-top-right-radius: 6px;
        }

        .order-items td {
            padding: 12px;
            background-color: #fbfbfb;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-items tr:nth-child(even) td {
            background-color: #f1f8f4;
        }

        .order-items tr:hover td {
            background-color: #e2f5df;
            transition: background-color 0.3s ease;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            margin-top: 30px;
            background-color: #2e7d32;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 16px;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-link:hover {
            background-color: #27642b;
            transform: translateY(-2px);
        }

        .back-link::before {
            content: "⬅";
            font-size: 18px;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }

            .order-items table,
            .order-items th,
            .order-items td {
                font-size: 13px;
            }

            h1 {
                font-size: 1.6em;
            }

            h2 {
                font-size: 1.4em;
            }

            .back-link {
                width: 100%;
                text-align: center;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1><a href="admin_dashboard.php" class="brand">AgriApp - Painel do Administrador</a></h1>
        </header>

        <section class="order-details-section">
            <h2>Detalhes do Pedido #<?php echo $order['order_id']; ?></h2>

            <div class="order-details">
                <h3>Informações do Pedido</h3>
                <p><strong>Consumidor:</strong> <?php echo htmlspecialchars($order['consumer_name']); ?></p>
                <p><strong>Agricultor:</strong> <?php echo htmlspecialchars($order['farmer_name']); ?></p>
                <p><strong>Data do Pedido:</strong> <?php echo $order['order_date']; ?></p>
                <p><strong>Produto(s):</strong> <?php echo htmlspecialchars($order['product_name']); ?></p>
                <p><strong>Total (Kzs):</strong> <?php echo number_format($order['total_value'], 2, ',', '.'); ?></p>
                <p><strong>Método de Pagamento: Cash</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                <p><strong>Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                <p><strong>Total de Itens:</strong> <?php echo $order['item_count']; ?></p>
            </div>

            <div class="order-items">
                <h3>Itens do Pedido</h3>
                <?php if (count($items) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th>Descrição</th>
                                <th>Quantidade</th>
                                <th>Preço (por unidade)</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['product_description']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price_at_order'], 2, ',', '.'); ?> Kzs</td>
                                    <td><?php echo number_format($item['quantity'] * $item['price_at_order'], 2, ',', '.'); ?> Kzs</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Este pedido não contém itens.</p>
                <?php endif; ?>
            </div>
        </section>

        <a href="admin_dashboard.php?page=orders" class="back-link">Voltar</a>
    </div>
</body>
</html>
