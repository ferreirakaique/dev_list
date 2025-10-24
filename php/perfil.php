<?php
include('conexao.php');
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario_sessao'])) {
    header('location:login.php');
    exit();
}

// Dados da sessão
$id_sessao = $_SESSION['id_usuario_sessao'];
$nome_usuario = $_SESSION['nome_usuario_sessao'];
$email_usuario = $_SESSION['email_usuario_sessao'];
$foto_usuario = $_SESSION['foto_usuario_sessao'];

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_digitado = $_POST['nome_usuario_digitado'];
    $email_digitado = $_POST['email_usuario_digitado'];
    $senha_digitada = $_POST['senha_usuario_digitado'];
    $confirmar_senha = $_POST['confirmar_senha_digitado'];

    // Verifica se uma nova imagem foi enviada
    $imagem_usuario_nova = null;
    if (!empty($_FILES['imagem_usuario_nova']['tmp_name'])) {
        $imagem_usuario_nova = file_get_contents($_FILES['imagem_usuario_nova']['tmp_name']);
    }

    // Verifica se as senhas coincidem (ou se o usuário não quis mudar)
    if (empty($senha_digitada) || $senha_digitada === $confirmar_senha) {

        if (!empty($senha_digitada)) {
            $senha_hash = password_hash($senha_digitada, PASSWORD_DEFAULT);
        } else {
            // mantém a senha antiga se o campo estiver vazio
            $stmt_old = $conexao->prepare("SELECT senha_usuario FROM usuarios WHERE id_usuario = ?");
            $stmt_old->bind_param("i", $id_sessao);
            $stmt_old->execute();
            $result_old = $stmt_old->get_result()->fetch_assoc();
            $senha_hash = $result_old['senha_usuario'];
        }

        if ($imagem_usuario_nova !== null) {
            // Atualiza com nova imagem
            $stmt = $conexao->prepare('UPDATE usuarios SET nome_usuario = ?, email_usuario = ?, senha_usuario = ?, foto_usuario = ? WHERE id_usuario = ?');
            $null = null;
            $stmt->bind_param('sssbi', $nome_digitado, $email_digitado, $senha_hash, $null, $id_sessao);
            $stmt->send_long_data(3, $imagem_usuario_nova);
        } else {
            // Atualiza sem alterar a imagem
            $stmt = $conexao->prepare('UPDATE usuarios SET nome_usuario = ?, email_usuario = ?, senha_usuario = ? WHERE id_usuario = ?');
            $stmt->bind_param('sssi', $nome_digitado, $email_digitado, $senha_hash, $id_sessao);
        }

        $stmt->execute();

        if ($stmt->affected_rows >= 0) {
            // Busca novamente a imagem atualizada no banco
            $stmt_img = $conexao->prepare('SELECT foto_usuario FROM usuarios WHERE id_usuario = ?');
            $stmt_img->bind_param('i', $id_sessao);
            $stmt_img->execute();
            $foto_atualizada = $stmt_img->get_result()->fetch_assoc()['foto_usuario'];

            // Atualiza as variáveis de sessão
            $_SESSION['nome_usuario_sessao'] = $nome_digitado;
            $_SESSION['email_usuario_sessao'] = $email_digitado;
            $_SESSION['foto_usuario_sessao'] = base64_encode($foto_atualizada);

            // Atualiza a variável usada na página
            $foto_usuario = $_SESSION['foto_usuario_sessao'];

            $edicoes_sucesso = true;
        }
    } else {
        $erro_senha = true;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil</title>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('nav.php'); ?>

    <main>
        <h1 id="titulo">Meu Perfil</h1>

        <form method="post" enctype="multipart/form-data" class="form-perfil">
            <div class="foto-container">
                <?php if (!empty($foto_usuario)): ?>
                    <img src="data:image/jpeg;base64,<?php echo htmlspecialchars($foto_usuario); ?>" alt="Foto do usuário" class="foto-perfil">
                <?php else: ?>
                    <img src="../img/perfil_padrao.png" alt="Foto padrão" class="foto-perfil">
                <?php endif; ?>

                <label for="foto_usuario">Alterar foto:</label>
                <input type="file" name="imagem_usuario_nova" id="foto_usuario" accept="image/*">
            </div>

            <label for="nome">Nome</label>
            <input type="text" name="nome_usuario_digitado" id="nome" required value="<?php echo htmlspecialchars($nome_usuario); ?>">

            <label for="email">Email</label>
            <input type="email" name="email_usuario_digitado" id="email" required value="<?php echo htmlspecialchars($email_usuario); ?>">

            <h2>Alterar Senha</h2>
            <label for="senha">Nova senha</label>
            <input type="password" name="senha_usuario_digitado" id="senha">

            <label for="confirmar_senha">Confirmar nova senha</label>
            <input type="password" name="confirmar_senha_digitado" id="confirmar_senha">

            <button type="submit" class="btn-salvar"><i class="fa-solid fa-floppy-disk"></i> Salvar Alterações</button>
        </form>
    </main>

    <?php if (isset($edicoes_sucesso) && $edicoes_sucesso): ?>
        <script>
            Swal.fire({
                title: "Sucesso!",
                text: "Edições salvas com sucesso!",
                icon: "success",
                confirmButtonColor: "#0a8383",
            });
        </script>
    <?php elseif (isset($erro_senha) && $erro_senha): ?>
        <script>
            Swal.fire({
                title: "Erro!",
                text: "As senhas não coincidem.",
                icon: "error",
                confirmButtonColor: "#d33",
            });
        </script>
    <?php endif; ?>
</body>

</html>