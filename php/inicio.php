<?php
include('conexao.php');
session_start();

$id_sessao = $_SESSION['id_usuario_sessao'];
if (!isset($id_sessao)) {
    header('location:login.php');
    exit();
}

$stmt_tarefas_usuario = $conexao->prepare('SELECT * FROM tarefas WHERE fk_id_usuario = ?');
$stmt_tarefas_usuario->bind_param('i', $id_sessao);
$stmt_tarefas_usuario->execute();
$result_tarefas_usuario = $stmt_tarefas_usuario->get_result();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/inicio.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Inicio</title>
</head>

<body>
    <?php include('nav.php') ?>
    <main>
        <h1 id="titulo">Minhas Tarefas</h1>
        <div class="container_tarefas">
            <?php if ($result_tarefas_usuario->num_rows > 0): ?>
                <?php while ($usuario = $result_tarefas_usuario->fetch_assoc()): ?>
                    <div class="tarefa">
                        <div class="status">
                            <?php if ($usuario['status_tarefa'] === 'Feito'): ?>
                                <span id="status_tarefa" style="background-color: green;color: white;border:1px solid white;"><?php echo htmlspecialchars($usuario['status_tarefa']); ?></span>
                            <?php elseif ($usuario['status_tarefa'] === 'Pendente'): ?>
                                <span id="status_tarefa"><?php echo htmlspecialchars($usuario['status_tarefa']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="titulo_tarefa">
                            <h1><?php echo htmlspecialchars($usuario['titulo_tarefa']); ?></h1>
                        </div>
                        <div class="descricao_tarefa">
                            <p><?php echo htmlspecialchars($usuario['descricao_tarefa']); ?></p>
                        </div>
                        <div class="operacoes">
                            <a href="editar_tarefa.php?id=<?php echo htmlspecialchars($usuario['id_tarefa']) ?>" class="editar">Editar</a>
                            <a href="excluir_tarefa.php?id=<?php echo htmlspecialchars($usuario['id_tarefa']) ?>" class="excluir">Excluir</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </main>
    <script>
        // Seleciona todos os botões "Excluir"
        document.querySelectorAll('.excluir').forEach(botao => {
            botao.addEventListener('click', function(event) {
                event.preventDefault(); // impede o link de ser seguido imediatamente

                const urlExclusao = this.getAttribute('href'); // pega o link original

                Swal.fire({
                    title: "Você deseja excluir esta tarefa?",
                    text: "Essa ação não pode ser desfeita!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#0a8383",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Sim, excluir!",
                    cancelButtonText: "Cancelar"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = urlExclusao; // redireciona se confirmar
                    }
                });
            });
        });
    </script>

</body>

</html>