<?php
// admin_orders.php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Pedidos - AgriApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #2e7d32;
            --secondary: #a5d6a7;
            --light: #f4f8f4;
            --text-dark: #333;
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
            color: var(--primary);
            font-size: 1.4rem;
            border-left: 6px solid var(--secondary);
            padding-left: 12px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 12px 16px;
            text-align: left;
        }

        th {
            background-color: var(--secondary);
            color: var(--primary);
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f1f8e9;
        }

        tr:hover {
            background-color: #dcedc8;
            transition: background 0.3s;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        p {
            margin-top: 10px;
            color: #777;
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

            td:nth-of-type(1)::before { content: "ID Pedido"; }
            td:nth-of-type(2)::before { content: "Consumidor"; }
            td:nth-of-type(3)::before { content: "Agricultor"; }
            td:nth-of-type(4)::before { content: "Data do Pedido"; }
            td:nth-of-type(5)::before { content: "Produto"; }
            td:nth-of-type(6)::before { content: "Total (Kzs)"; }
            td:nth-of-type(7)::before { content: "Status"; }
            td:nth-of-type(8)::before { content: "Itens"; }
            td:nth-of-type(9)::before { content: "Ações"; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Lista de Pedidos</h2>

    <?php
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
        ORDER BY p.order_date DESC
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Consumidor</th>
                        <th>Agricultor</th>
                        <th>Data do Pedido</th>
                        <th>Produto</th>
                        <th>Total (Kzs)</th>
                        <th>Status</th>
                        <th>Itens</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['order_id']}</td>
                    <td>{$row['consumer_name']}</td>
                    <td>{$row['farmer_name']}</td>
                    <td>{$row['order_date']}</td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['total_value']}</td>
                    <td>{$row['status']}</td>
                    <td>{$row['item_count']}</td>
                    <td>
                        <a href='ver_pedido.php?id={$row['order_id']}'>Ver Detalhes</a>
                    </td>
                </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nenhum pedido encontrado.</p>";
    }
    ?>
</div>

</body>
</html>
