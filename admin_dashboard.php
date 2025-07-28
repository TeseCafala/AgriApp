<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$admin_id = $_SESSION['admin_id'] ?? 1;

$query = "SELECT name, email, profile_image FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    $profile_image_name = $admin['profile_image'] ?? '';
    $uploads_dir = 'uploads/';

    if (!empty($profile_image_name) && file_exists($uploads_dir . $profile_image_name)) {
        $profile_image_path = htmlspecialchars($uploads_dir . $profile_image_name);
    } else {
        $profile_image_path = 'assets/imgs/default_admin.png';
    }
} else {
    error_log("Failed to prepare admin data query: " . $conn->error);
    $admin = ['name' => 'Admin', 'email' => ''];
    $profile_image_path = 'assets/imgs/default_admin.png';
}

$total_registered_users_count = 0;

// Contar consumidores
$query_consumers = "SELECT COUNT(id) AS total_consumers FROM consumer";
$result_consumers = $conn->query($query_consumers);
$total_consumers = 0;
if ($result_consumers && $row = $result_consumers->fetch_assoc()) {
    $total_consumers = (int)$row['total_consumers'];
}

// Contar agricultores
$query_farmers = "SELECT COUNT(id) AS total_farmers FROM farmers_";
$result_farmers = $conn->query($query_farmers);
$total_farmers = 0;
if ($result_farmers && $row = $result_farmers->fetch_assoc()) {
    $total_farmers = (int)$row['total_farmers'];
}

// Soma total
$total_registered_users_count = $total_consumers + $total_farmers;


$completed_orders_count = 0;
$query_completed_orders = "SELECT COUNT(order_id) AS total_completed_orders FROM pedidos_ WHERE status = 'Completed'";
$result_completed_orders = $conn->query($query_completed_orders);
if ($result_completed_orders && $row_completed_orders = $result_completed_orders->fetch_assoc()) {
    $completed_orders_count = $row_completed_orders['total_completed_orders'];
}

$total_revenue = 0.00;
$query_total_revenue = "SELECT SUM(total_value) AS total_revenue FROM pedidos_ WHERE status = 'Completed'";
$result_total_revenue = $conn->query($query_total_revenue);
if ($result_total_revenue && $row_total_revenue = $result_total_revenue->fetch_assoc()) {
    $total_revenue = $row_total_revenue['total_revenue'] ?? 0.00;
}

$query_stats = "
    SELECT
        stats.order_date,
        stats.total_orders,
        stats.total_sales
    FROM (
        SELECT
            DATE(p.order_date) AS order_date,
            COUNT(DISTINCT p.order_id) AS total_orders,
            SUM(oi.quantity * oi.price_at_order) AS total_sales
        FROM pedidos_ p
        LEFT JOIN order_items oi ON p.order_id = oi.order_id
WHERE p.order_date >= CURDATE() - INTERVAL 15 DAY
        GROUP BY DATE(p.order_date)
    ) AS stats
    ORDER BY stats.order_date ASC
";

$result_stats = mysqli_query($conn, $query_stats);

$dates = [];
$total_orders_chart = [];
$total_sales_chart = [];

$today = new DateTime();
$fifteen_days_ago = (new DateTime())->sub(new DateInterval('P14D'));
$period = new DatePeriod($fifteen_days_ago, new DateInterval('P1D'), $today->add(new DateInterval('P1D')));

$chart_data_map = [];
while ($row = mysqli_fetch_assoc($result_stats)) {
    $chart_data_map[$row['order_date']] = [
        'orders' => (int)$row['total_orders'],
        'sales' => (float)$row['total_sales']
    ];
}

foreach ($period as $date) {
    $formatted_date = $date->format('Y-m-d');
    $dates[] = $formatted_date;
    $total_orders_chart[] = $chart_data_map[$formatted_date]['orders'] ?? 0;
    $total_sales_chart[] = $chart_data_map[$formatted_date]['sales'] ?? 0.00;
}

$conn->close();
?>

        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Painel do Administrador - AgriApp</title>
            <link rel="stylesheet" href="admin_dashboard.css">
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
        </head>
        <body>
            

                <header class="main-header">
    <div class="header-left">
    <a href="admin_dashboard.php" class="logo-title">AgriApp - <span>Painel do Administrador</span></a>
</div>

    <div class="header-right">
        <div class="profile-menu">
            <a href="#" class="profile-dropdown-toggle">
                <img src="<?= $profile_image_path ?>" alt="Perfil do Admin" class="profile-picture-header">
                <span><?= htmlspecialchars($admin['name'] ?? 'Admin') ?></span>
                <ion-icon name="chevron-down-outline"></ion-icon>
            </a>
            <div class="profile-dropdown-menu">
                <a href="edit_profile.php"><ion-icon name="person-outline"></ion-icon> Editar Perfil</a>
                <a href="logout.php"><ion-icon name="log-out-outline"></ion-icon> Sair</a>
            </div>
        </div>
    </div>
</header>

           

            <nav class="main-nav">
                
                <a href="?page=home" class="<?= (!isset($_GET['page']) || $_GET['page'] == 'home') ? 'active' : '' ?>">
                    <ion-icon name="home-outline"></ion-icon> Início
                </a>
                <a href="?page=users" class="<?= ($_GET['page'] ?? '') == 'users' ? 'active' : '' ?>">
                    <ion-icon name="people-outline"></ion-icon> Usuários
                </a>
                <a href="?page=orders" class="<?= ($_GET['page'] ?? '') == 'orders' ? 'active' : '' ?>">
                    <ion-icon name="receipt-outline"></ion-icon> Pedidos
                </a>
                <a href="?page=products" class="<?= ($_GET['page'] ?? '') == 'products' ? 'active' : '' ?>">
                    <ion-icon name="cube-outline"></ion-icon> Produtos
                </a>
            </nav>

            <div class="content">
                <?php
                $page = $_GET['page'] ?? 'home';

                if ($page == 'users') {
                    include 'admin_users.php';
                } elseif ($page == 'orders') {
                    include 'admin_orders.php';
                } elseif ($page == 'products') {
                    include 'admin_products.php';
                } elseif ($page == 'messages') {
                    include 'admin_messages.php';
                } else {
                    // Página inicial do dashboard com gráfico
                    ?>
                    <section class="dashboard-overview">
                        <h2>Bem-vindo, <?= htmlspecialchars($admin['name'] ?? 'Admin') ?>!</h2>
                        <p>Aqui você pode ver uma visão geral do sistema e navegar pelas funcionalidades administrativas.</p>

                        <div class="summary-cards">
                            <div class="card-item">
    <a href="http://localhost/agriapp/admin/admin_dashboard.php?page=users">
        <ion-icon name="person-add-outline"></ion-icon>
    </a>
    <h3>Total de Usuários Registrados</h3>
    <p><?= $total_registered_users_count ?></p>
</div>

                            <div class="card-item">
    <a href="http://localhost/agriapp/admin/admin_dashboard.php?page=orders">
        <ion-icon name="cart-outline"></ion-icon>
    </a>
    <h3>Pedidos Concluídos</h3>
    <p><?= $completed_orders_count ?></p>
</div>

                            <div class="card-item">
                                <ion-icon name="cash-outline"></ion-icon>
                                <h3>Receita Total</h3>
                                <p>Kz <?= number_format($total_revenue, 2, ',', '.') ?></p>
                            </div>
                        </div>
                    </section>

                    <section class="chart-section">
                            <h2>Vendas e Pedidos dos Últimos 15 Dias</h2>
                        <div class="chart-container">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </section>

                    <script>
                        const ctx = document.getElementById('salesChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: <?= json_encode($dates) ?>,
                                datasets: [{
                                    label: 'Número de Pedidos',
                                    data: <?= json_encode($total_orders_chart) ?>,
                                    borderColor: 'rgba(46, 125, 50, 1)', // Darker green
                                    backgroundColor: 'rgba(46, 125, 50, 0.3)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointBackgroundColor: 'rgba(46, 125, 50, 1)'
                                }, {
                                    label: 'Valor das Vendas (Kzs)',
                                    data: <?= json_encode($total_sales_chart) ?>,
                                    borderColor: 'rgba(0, 123, 255, 1)', // A good blue for contrast
                                    backgroundColor: 'rgba(0, 123, 255, 0.3)',
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 3,
                                    pointBackgroundColor: 'rgba(0, 123, 255, 1)'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false, // Allows flexible sizing
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'top',
                                        labels: {
                                            font: {
                                                size: 14
                                            }
                                        }
                                    },
                                    tooltip: {
                                        mode: 'index',
                                        intersect: false,
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.dataset.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                if (context.dataset.label === 'Valor das Vendas (Kzs)') {
                                                    return label + 'Kz ' + context.raw.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                                                }
                                                return label + context.raw.toLocaleString('pt-BR');
                                            }
                                        },
                                        titleFont: { size: 16 },
                                        bodyFont: { size: 14 },
                                        padding: 10
                                    }
                                },
                                scales: {
                                    x: {
                                        type: 'category',
                                        title: {
                                            display: true,
                                            text: 'Data',
                                            font: { size: 16 }
                                        },
                                        ticks: {
                                            font: { size: 12 }
                                        }
                                    },
                                    y: {
                                        beginAtZero: true,
                                        title: {
                                            display: true,
                                            text: 'Valor / Contagem',
                                            font: { size: 16 }
                                        },
                                        ticks: {
                                            callback: function(value, index, values) {
                                                // Format values appropriately
                                                if (this.max > 1000) { // Simple heuristic for larger numbers (sales)
                                                    return value.toLocaleString('pt-BR'); // Format with thousands separator
                                                }
                                                return value; // For smaller numbers (counts)
                                            },
                                            font: { size: 12 }
                                        }
                                    }
                                }
                            }
                        });
                    </script>
                    <?php
                }
                ?>
            </div>
<script>
    // Toggle dropdown
    document.querySelector('.profile-dropdown-toggle').addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector('.profile-dropdown-menu').classList.toggle('show');
    });

    // Fechar ao clicar fora
    window.addEventListener('click', function (e) {
        if (!e.target.closest('.profile-menu')) {
            const dropdownMenu = document.querySelector('.profile-dropdown-menu');
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            }
        }
    });

    
</script>

        </body>
        </html>