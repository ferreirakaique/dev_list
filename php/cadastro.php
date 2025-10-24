<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_usuario = $_POST['nome_usuario_digitado'];
    $email_usuario = $_POST['email_usuario_digitado'];
    $senha_usuario = $_POST['senha_usuario_digitado'];
    $senha_criptografada = password_hash($senha_usuario, PASSWORD_DEFAULT);

    // Verifica se já existe usuário com o mesmo e-mail
    $stmt_verifica = $conexao->prepare('SELECT id_usuario FROM usuarios WHERE email_usuario = ?');
    $stmt_verifica->bind_param('s', $email_usuario);
    $stmt_verifica->execute();
    $resultado_verifica = $stmt_verifica->get_result();

    if ($resultado_verifica->num_rows > 0) {
        $email_existente = true;
    } else {
        // Lê e converte a foto enviada
        $foto_usuario = null;
        if (!empty($_FILES['foto_usuario_digitado']['tmp_name'])) {
            $foto_usuario = file_get_contents($_FILES['foto_usuario_digitado']['tmp_name']);
        }

        // Insere o usuário
        $stmt = $conexao->prepare('INSERT INTO usuarios (nome_usuario, email_usuario, senha_usuario, foto_usuario) VALUES (?, ?, ?, ?)');
        $null = null;
        $stmt->bind_param('sssb', $nome_usuario, $email_usuario, $senha_criptografada, $null);
        $stmt->send_long_data(3, $foto_usuario);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $cadastro_sucesso = true;
        } else {
            $erro_cadastro = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário</title>
    <link rel="stylesheet" href="../css/cadastro.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <main>
        <form method="post" enctype="multipart/form-data">
            <a href="login.php" class="voltar"><i class="fa-solid fa-right-from-bracket"></i> Voltar</a>
            <h1>Cadastrar</h1>

            <label for="nome">Nome</label>
            <input type="text" name="nome_usuario_digitado" id="nome" required placeholder="Seu nome completo">

            <label for="email">Email</label>
            <input type="email" name="email_usuario_digitado" id="email" required placeholder="exemplo@email.com">

            <label for="senha">Senha</label>
            <input type="password" name="senha_usuario_digitado" id="senha" required placeholder="Crie uma senha">

            <label for="foto">Foto de perfil</label>
            <input type="file" accept="image/*" name="foto_usuario_digitado" id="foto" required>

            <button type="submit"><i class="fa-solid fa-user-plus"></i> Criar usuário</button>
        </form>
    </main>

    <?php if (isset($cadastro_sucesso) && $cadastro_sucesso): ?>
        <script>
            Swal.fire({
                title: "Sucesso!",
                text: "Cadastro realizado com sucesso! Você será redirecionado para o login.",
                icon: "success",
                confirmButtonColor: "#0a8383",
            }).then(() => {
                window.location = 'login.php';
            });
        </script>
    <?php elseif (isset($email_existente) && $email_existente): ?>
        <script>
            Swal.fire({
                title: "Atenção!",
                text: "Este e-mail já está cadastrado. Tente outro.",
                icon: "warning",
                confirmButtonColor: "#0a8383",
            });
        </script>
    <?php elseif (isset($erro_cadastro) && $erro_cadastro): ?>
        <script>
            Swal.fire({
                title: "Erro!",
                text: "Não foi possível realizar o cadastro. Tente novamente.",
                icon: "error",
                confirmButtonColor: "#0a8383",
            });
        </script>
    <?php endif; ?>
</body>

</html>