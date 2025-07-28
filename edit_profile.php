<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

$admin_id = 1;

// Buscar dados atuais
$query = "SELECT * FROM admin WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $admin['password'];
    $profileImage = $admin['profile_image'];

    // Pasta de uploads
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Upload da imagem
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);

        if (in_array($fileType, $allowedTypes)) {
            $ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
            $imageName = 'admin_' . time() . '.' . $ext;
            $destPath = $uploadDir . $imageName;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $destPath)) {
                $profileImage = $imageName;
            }
        }
    }

    // Atualizar no banco
    $update = $conn->prepare("UPDATE admin SET name=?, email=?, phone=?, password=?, profile_image=? WHERE id=?");
    $update->bind_param("sssssi", $name, $email, $phone, $password, $profileImage, $admin_id);
    $update->execute();

    echo "<p>Perfil atualizado com sucesso!</p>";
    header("Refresh:2; url=edit_profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Admin</title>
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: "Segoe UI", sans-serif;
    }

    body {
        background-color: #f1f5f9;
        color: #333;
        padding: 30px 16px;
        min-height: 100vh;
    }

    h2 {
        text-align: center;
        color: #2e7d32;
        margin-bottom: 15px;
        font-size: 22px;
    }

    form {
        background-color: #fff;
        max-width: 420px;
        margin: 0 auto;
        padding: 20px 24px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    label {
        display: block;
        margin-top: 14px;
        font-weight: 600;
        color: #444;
        font-size: 15px;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    input[type="file"] {
        width: 100%;
        padding: 8px 10px;
        margin-top: 6px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    input[type="file"] {
        background-color: #f9f9f9;
    }

    button {
        width: 100%;
        margin-top: 20px;
        padding: 10px;
        background-color: #2e7d32;
        color: white;
        font-size: 15px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #1b5e20;
    }

    img {
        display: block;
        margin: 0 auto 15px auto;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #2e7d32;
    }

    p.no-image {
        text-align: center;
        margin-bottom: 15px;
        color: #777;
    }

    a {
        display: block;
        text-align: center;
        margin-top: 18px;
        color: #2e7d32;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }

    a:hover {
        text-decoration: underline;
    }
</style>


</head>
<body>
    <h2>Editar Perfil do Admin</h2>

  <?php if (!empty($admin['profile_image']) && file_exists('uploads/' . $admin['profile_image'])): ?>
    <img src="uploads/<?= htmlspecialchars($admin['profile_image']) ?>" alt="Foto de Perfil">
<?php else: ?>
    <p class="no-image">Sem foto de perfil</p>
<?php endif; ?>


    <form method="POST" enctype="multipart/form-data">
        <label>Nome:
            <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>
        </label>
        <label>Email:
            <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>
        </label>
        <label>Telefone:
            <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone']) ?>" required>
        </label>
        <label>Nova Senha (deixe em branco para manter a atual):
            <input type="password" name="password">
        </label>
        <label>Foto de Perfil:
            <input type="file" name="profile_image" accept="image/*">
        </label>
        <br>
        <button type="submit">Salvar Alterações</button>
        <p><a href="admin_dashboard.php">← Voltar ao Dashboard</a></p>
    </form>
</body>
</html>
