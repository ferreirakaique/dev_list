<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['id_usuario_sessao'])) {
    header('location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('location:gerenciar_usuarios.php');
    exit();
}

$id_usuario = intval($_GET['id']);

$stmt = $conexao->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param('i', $id_usuario);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_usuario'];
    $email = $_POST['email_usuario'];

    if (!empty($_FILES['foto_usuario']['tmp_name'])) {
        $foto = file_get_contents($_FILES['foto_usuario']['tmp_name']);
        $stmt = $conexao->prepare("UPDATE usuarios SET nome_usuario = ?, email_usuario = ?, foto_usuario = ? WHERE id_usuario = ?");
        $null = null;
        $stmt->bind_param("ssbi", $nome, $email, $null, $id_usuario);
        $stmt->send_long_data(2, $foto);
    } else {
        $stmt = $conexao->prepare("UPDATE usuarios SET nome_usuario = ?, email_usuario = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssi", $nome, $email, $id_usuario);
    }

    $stmt->execute();

    echo "<script>
        alert('Usuário atualizado com sucesso!');
        window.location = 'gerenciar_usuarios.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../css/editar_usuario.css">
</head>

<body>
    <?php include('nav.php'); ?>

    <main>
        <div class="container">
            <h1>Editar Usuário</h1>
            <form method="post" enctype="multipart/form-data">
                <label>Nome</label>
                <input type="text" name="nome_usuario" value="<?php echo htmlspecialchars($usuario['nome_usuario']); ?>" required>
                <label>Email</label>
                <input type="email" name="email_usuario" value="<?php echo htmlspecialchars($usuario['email_usuario']); ?>" required>
                <label>Nova Foto (opcional)</label>
                <input type="file" name="foto_usuario" accept="image/*">
                <button type="submit">Salvar</button>
            </form>
        </div>
    </main>
</body>

</html>