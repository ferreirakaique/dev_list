<?php
$id_sessao = $_SESSION['id_usuario_sessao'];
$usuario_nome = $_SESSION['nome_usuario_sessao'];
$email_usuario = $_SESSION['email_usuario_sessao'];
$foto_usuario = $_SESSION['foto_usuario_sessao'];

if (!isset($id_sessao)) {
    header('location:login.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <nav>
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="adicionar_tarefas.php">Adicionar Tarefas</a></li>
            <li><a href="gerenciar_usuarios.php">Gerenciar Usuarios</a></li>
            <li id="imagem_perfil">
                <a href="perfil.php">Olá, <?php echo $usuario_nome ?>
                    <img src="data:jpeg/image;base64,<?php echo htmlspecialchars($foto_usuario) ?>" alt="Foto do usuário">
                </a>
                <i class="fa-solid fa-right-from-bracket" id="sair"></i>
            </li>
        </ul>
    </nav>
    <script>
        document.getElementById('sair').addEventListener('click', () => {
            Swal.fire({
                title: "Você deseja sair?",
                text: "Você não pode reverter isso",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#0a8383",
                cancelButtonColor: "#d33",
                confirmButtonText: "Sim, sair!"
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = 'logout.php';
                }
            });
        })
    </script>
</body>

</html>