<?php
include('conexao.php');
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario_sessao'])) {
    header('location:login.php');
    exit();
}

// Consulta todos os usuários
$stmt = $conexao->prepare("SELECT id_usuario, nome_usuario, email_usuario, foto_usuario FROM usuarios");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários</title>
    <link rel="stylesheet" href="../css/gerenciar_usuarios.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include('nav.php'); ?>

    <main>
        <div class="container">
            <h1 id="titulo">Gerenciar Usuários</h1>

            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($usuario = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($usuario['foto_usuario'])): ?>
                                        <img src="data:image/jpeg;base64,<?php echo base64_encode($usuario['foto_usuario']); ?>" class="foto-usuario">
                                    <?php else: ?>
                                        <img src="../img/perfil_padrao.png" class="foto-usuario">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($usuario['nome_usuario']); ?></td>
                                <td><?php echo htmlspecialchars($usuario['email_usuario']); ?></td>
                                <td class="acoes">
                                    <a href="editar_usuario.php?id=<?php echo $usuario['id_usuario']; ?>" class="btn-editar">
                                        <i class="fa-solid fa-pen"></i> Editar
                                    </a>
                                    <a href="#" data-id="<?php echo $usuario['id_usuario']; ?>" class="btn-excluir">
                                        <i class="fa-solid fa-trash"></i> Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">Nenhum usuário encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </main>

    <script>
        // Botão excluir com SweetAlert
        document.querySelectorAll('.btn-excluir').forEach(botao => {
            botao.addEventListener('click', (e) => {
                e.preventDefault();
                const idUsuario = botao.getAttribute('data-id');

                Swal.fire({
                    title: "Você tem certeza?",
                    text: "O usuário será excluído permanentemente!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0a8383",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sim, excluir!",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location = "excluir_usuario.php?id=" + idUsuario;
                    }
                });
            });
        });
    </script>
</body>

</html>