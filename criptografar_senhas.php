<?php
require 'config.php';

// Atualiza senha dos Admins
$admins = $conn->query("SELECT id, password FROM admin");
while ($admin = $admins->fetch_assoc()) {
    if (password_get_info($admin['password'])['algo'] === 0) { // se não está criptografada
        $hashed = password_hash($admin['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password='$hashed' WHERE id={$admin['id']}");
    }
}

// Atualiza senha dos Farmers
$farmers = $conn->query("SELECT id, password FROM farmers_");
while ($farmer = $farmers->fetch_assoc()) {
    if (password_get_info($farmer['password'])['algo'] === 0) {
        $hashed = password_hash($farmer['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE farmers_ SET password='$hashed' WHERE id={$farmer['id']}");
    }
}

// Atualiza senha dos Consumers
$consumers = $conn->query("SELECT id, password FROM consumer");
while ($consumer = $consumers->fetch_assoc()) {
    if (password_get_info($consumer['password'])['algo'] === 0) {
        $hashed = password_hash($consumer['password'], PASSWORD_DEFAULT);
        $conn->query("UPDATE consumer SET password='$hashed' WHERE id={$consumer['id']}");
    }
}

echo "Senhas criptografadas com sucesso!";
?>
