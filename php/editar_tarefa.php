<?php
include('conexao.php');
session_start();

$id_sessao = $_SESSION['id_usuario_sessao'];
if (!isset($id_sessao)) {
    header('location:login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('location:inicio.php');
}

$id_tarefa = intval($_GET['id']);

$stmt = $conexao->prepare('SELECT * FROM tarefas WHERE id_tarefa = ?');
$stmt->bind_param('i', $id_tarefa);
$stmt->execute();
$result = $stmt->get_result();
$tarefa = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descricao = $_POST['descricao'];
    $data = $_POST['data'];
    $status = $_POST['status'];

    $stmt_editar = $conexao->prepare('UPDATE tarefas SET titulo_tarefa = ?, descricao_tarefa = ?, data_tarefa = ?, status_tarefa = ? WHERE id_tarefa = ?');
    $stmt_editar->bind_param('ssssi', $titulo, $descricao, $data, $status, $id_tarefa);
    $stmt_editar->execute();
    $edicoes_sucesso = true;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/editar_tarefa.css">
    <title>Editar Tarefa</title>
</head>

<body>
    <?php include('nav.php') ?>
    <main>
        <div class="container">
            <a href="inicio.php" class="btn-voltar">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1 id="titulo">Editar Tarefa</h1>
            <form method="post">
                <label>Título:</label>
                <input type="text" name="titulo" value="<?php echo htmlspecialchars($tarefa['titulo_tarefa']); ?>" required>

                <label>Descrição:</label>
                <input type="text" name="descricao" value="<?php echo htmlspecialchars($tarefa['descricao_tarefa']); ?>" required>

                <label>Data:</label>
                <input type="date" name="data" value="<?php echo htmlspecialchars($tarefa['data_tarefa']); ?>" required>

                <label>Status:</label>
                <select name="status" required>
                    <option value="Pendente" <?php echo $tarefa['status_tarefa'] === 'Pendente' ? 'selected' : ''; ?>>Pendente</option>
                    <option value="Feito" <?php echo $tarefa['status_tarefa'] === 'Feito' ? 'selected' : ''; ?>>Feito</option>
                </select>

                <button type="submit" name="salvar">Salvar</button>
            </form>
        </div>
    </main>

    <?php if (isset($edicoes_sucesso) && $edicoes_sucesso): ?>
        <script>
            Swal.fire({
                title: "Sucesso",
                text: "Edições salvas com sucesso",
                icon: "success",
                confirmButtonColor: '#0a8383',
            }).then(() => {
                window.location = 'inicio.php';
            });
        </script>
    <?php endif; ?>
</body>

</html>