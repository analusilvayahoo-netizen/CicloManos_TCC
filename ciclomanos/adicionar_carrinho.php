<?php
session_start();
include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }
if (!$conn) { die("Erro: Conexão com a base de dados não encontrada."); }

// Verifica se o cliente está logado corretamente
if (($_SESSION['tipo_usuario'] ?? '') !== 'cliente' || empty($_SESSION['id_cliente'])) {
    header('Location: login.php');
    exit;
}

$idCliente = (int) $_SESSION['id_cliente'];
$idProd = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idProd > 0) {
    // Insere ou atualiza a quantidade caso o produto já esteja no carrinho deste cliente (conforme a restrição UNIQUE do seu SQL)
    $stmt = mysqli_prepare($conn, "INSERT INTO carrinho (id_cliente, id_produto, quantidade) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE quantidade = quantidade + 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $idCliente, $idProd);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

// Redireciona de imediato para a página do carrinho para exibir os itens guardados na conta
header('Location: carrinho.php');
exit;