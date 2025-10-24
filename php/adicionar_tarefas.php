<?php
include('conexao.php');
session_start();

$id_sessao = $_SESSION['id_usuario_sessao'];

if (!isset($id_sessao)) {
    header('location:login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo_tarefa = $_POST['titulo_digitado'];
    $descricao_tarefa = $_POST['descricao_digitado'];
    $data_tarefa = $_POST['data_digitado'];
    $status_tarefa = $_POST['status_digitado'];

    $stmt = $conexao->prepare('INSERT INTO tarefas (titulo_tarefa,descricao_tarefa,data_tarefa,status_tarefa,fk_id_usuario) VALUES (?,?,?,?,?)');
    $stmt->bind_param('ssssi', $titulo_tarefa, $descricao_tarefa, $data_tarefa, $status_tarefa, $id_sessao);
    $stmt->execute();
    $criacao_tarefa = true;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/adicionar_tarefas.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Adicionar Tarefa</title>
</head>

<body>
    <?php include('nav.php') ?>
    <main>
        <div class="container">
            <form method="post">
                <a href="inicio.php" class="btn-voltar">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h1>Adicionar Tarefa</h1>
                <label for="">Titulo tarefa</label>
                <input type="text" name="titulo_digitado" id="" required>
                <label for="">Descrição</label>
                <input type="text" name="descricao_digitado" id="" required>
                <label for="">Data</label>
                <input type="date" name="data_digitado" id="">

                <input type="hidden" value="Pendente" name="status_digitado" id="" required>
                <button type="submit">Criar tarefa</button>
            </form>
        </div>
    </main>
    </div>
    <?php if (isset($criacao_tarefa) && $criacao_tarefa): ?>
        <script>
            Swal.fire({
                title: "Sucesso",
                text: "Tarefa concluida com êxito",
                icon: "success",
                confirmButtonColor: '#0a8383',
            }).then(() => {
                window.location = 'inicio.php';
            });
        </script>
    <?php endif; ?>
</body>

</html>