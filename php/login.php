<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_usuario = $_POST['email_usuario_digitado'];
    $senha_usuario = $_POST['senha_usuario_digitado'];

    $stmt = $conexao->prepare('SELECT * FROM usuarios WHERE email_usuario = ?');
    $stmt->bind_param('s', $email_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        if (password_verify($senha_usuario, $usuario['senha_usuario'])) {
            session_start();
            $_SESSION['id_usuario_sessao'] = $usuario['id_usuario'];
            $_SESSION['nome_usuario_sessao'] = $usuario['nome_usuario'];
            $_SESSION['email_usuario_sessao'] = $usuario['email_usuario'];
            $_SESSION['foto_usuario_sessao'] = base64_encode($usuario['foto_usuario']);
            $login_sucesso = true;
        }
    } else {
        $login_errado = true;
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Login</title>
</head>

<body>
    <main>
        <form method="post">
            <h1>Login</h1>
            <label for="">Email</label>
            <input type="email" name="email_usuario_digitado" id="" required>
            <label for="">Senha</label>
            <input type="password" name="senha_usuario_digitado" id="" required>
            <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
            <button type="submit">Entrar</button>
        </form>
    </main>

    <?php if (isset($login_sucesso) && $login_sucesso): ?>
        <script>
            Swal.fire({
                title: "Sucesso",
                text: "Você sera redirecionado para a pagina de inicio",
                icon: "success",
                confirmButtonColor: '#0a8383',
            }).then(() => {
                window.location = 'inicio.php';
            });
        </script>
    <?php endif; ?>
    <?php if (isset($login_errado) && $login_errado): ?>
        <script>
            Swal.fire({
                title: "Erro",
                text: "Credenciais inválidas",
                icon: "error",
                confirmButtonColor: '#0a8383',
            })
        </script>
    <?php endif; ?>
</body>

</html>