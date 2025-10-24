<?php
include('conexao.php');
session_start();

if (!isset($_SESSION['id_usuario_sessao'])) {
    header('location:login.php');
    exit();
}

if (isset($_GET['id'])) {
    $id_usuario = intval($_GET['id']);

    $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();

    header('location:gerenciar_usuarios.php');
    exit();
} else {
    header('location:gerenciar_usuarios.php');
    exit();
}
