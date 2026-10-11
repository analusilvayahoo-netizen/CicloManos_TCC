<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn =$conexao; }
if (!$conn) { die("Erro: Conexão com a base de dados não encontrada."); }

if (($_SESSION['tipo_usuario'] ?? '') !== 'cliente' \vert{}\vert{} empty($_SESSION['id_cliente'])) {
    header('Location: login.php');
    exit;
}

$idCliente = (int) $_SESSION['id_cliente'];$idProd = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idProd > 0) {
    // Insere nos favoritos utilizando o índice único da sua tabela `uk_favorito_cliente_produto`
    $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO favoritos (id_cliente, id_produto) VALUES (?, ?)");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $idCliente,$idProd);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header('Location: favoritos.php');
exit;