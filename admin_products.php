<?php
// admin_products.php
require 'config.php';
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Lista de Produtos - AgriApp</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        :root {
            --primary: #2e7d32;
            --secondary: #a5d6a7;
            --light: #f4f8f4;
            --text-dark: #333;
            --danger: #e53935;
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

        img {
            max-width: 80px;
            border-radius: 6px;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-danger {
            background-color: var(--danger);
            color: #fff;
        }

        .btn-danger:hover {
            background-color: #c62828;
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
            td:nth-of-type(3)::before { content: "Quantidade"; }
            td:nth-of-type(4)::before { content: "Descrição"; }
            td:nth-of-type(5)::before { content: "Preço (Kzs)"; }
            td:nth-of-type(6)::before { content: "Agricultor"; }
            td:nth-of-type(7)::before { content: "Imagem"; }
            td:nth-of-type(8)::before { content: "Ações"; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Lista de Produtos</h2>

    <?php
    $query = "
        SELECT 
            p.id,
            p.name,
            p.quantity_avaliable,
            p.description,
            p.price,
            p.imagem,
            f.name AS farmer_name,
            p.ativo
        FROM productsz p
        LEFT JOIN farmers_ f ON p.farmer_id = f.id
        ORDER BY p.id DESC
    ";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Quantidade</th>
                        <th>Descrição</th>
                        <th>Preço (Kzs)</th>
                        <th>Agricultor</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['quantity_avaliable']}</td>
                    <td>{$row['description']}</td>
                    <td>" . number_format($row['price'], 2, ',', '.') . "</td>
                    <td>{$row['farmer_name']}</td>
                    <td>";

            if ($row['imagem']) {
                $image_path = '../agricultor/uploads/' . $row['imagem'];
                if (file_exists($image_path)) {
                    echo "<img src='$image_path' alt='{$row['name']}'>";
                } else {
                    echo "Imagem não encontrada";
                }
            } else {
                echo "Sem imagem";
            }

            echo "</td>
                    <td>";
          if ($row['ativo'] == 1) {
    echo "<a href='arquivar_produto.php?id={$row['id']}' 
             onclick=\"return confirm('Deseja arquivar este produto?');\"
             style='
                display:inline-block;
                background-color:#d32f2f;
                color:#fff;
                padding:8px 16px;
                border-radius:5px;
                text-decoration:none;
                font-weight:bold;
                font-size:14px;
            '
            onmouseover=\"this.style.backgroundColor='#b71c1c'\"
            onmouseout=\"this.style.backgroundColor='#d32f2f'\"
         >Arquivar</a>";
} else {
    echo "<a href='restaurar_produto.php?id={$row['id']}'
             onclick=\"return confirm('Deseja restaurar este produto?');\"
             style='
                display:inline-block;
                background-color:#388e3c;
                color:#fff;
                padding:8px 16px;
                border-radius:5px;
                text-decoration:none;
                font-weight:bold;
                font-size:14px;
            '
            onmouseover=\"this.style.backgroundColor='#1b5e20'\"
            onmouseout=\"this.style.backgroundColor='#388e3c'\"
         >Restaurar</a>";
}

            echo "</td>
                </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p>Nenhum produto encontrado.</p>";
    }
    ?>
</div>

</body>
</html>
