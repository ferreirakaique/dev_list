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
    exit();
}

$id_tarefa = intval($_GET['id']);

$stmt = $conexao->prepare("DELETE FROM tarefas WHERE id_tarefa = ?");
$stmt->bind_param('i', $id_tarefa);
$stmt->execute();
header('location:inicio.php');
