<?php
session_start();
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login_funcionario.php');
    exit;
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config.php';
if (!isset($conn) && isset($conexao)) { $conn = $conexao; }

$mensagem = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn) {
        $nome          = mysqli_real_escape_string($conn, $_POST['name']);
        $descricao     = mysqli_real_escape_string($conn, $_POST['descricao']);
        $imagem        = mysqli_real_escape_string($conn, $_POST['imagem']);
        $id_marca      = mysqli_real_escape_string($conn, $_POST['id_marca']);
        $modelo        = mysqli_real_escape_string($conn, $_POST['modelo']);
        $preco_venda   = mysqli_real_escape_string($conn, $_POST['preco_venda']);
        $qtd_atual     = mysqli_real_escape_string($conn, $_POST['qtd_atual']);
        $estoque_minimo= mysqli_real_escape_string($conn, $_POST['estoque_minimo']);
        $nomeCat       = trim($_POST['id_categoria']); // Recebe o nome digitado pelo funcionário
        $modalidade    = mysqli_real_escape_string($conn, $_POST['modalidade']);
        $tamanho_aro   = mysqli_real_escape_string($conn, $_POST['tamanho_aro']);
        $material      = mysqli_real_escape_string($conn, $_POST['material']);
        $cor           = mysqli_real_escape_string($conn, $_POST['cor']);

        // Converte a categoria digitada (ex: bicicletas, peças) no ID correspondente no banco
        $stmtCat = mysqli_prepare($conn, "SELECT id_categoria FROM categorias WHERE nome_categoria LIKE ? LIMIT 1");
        $termoCat = "%{$nomeCat}%";
        mysqli_stmt_bind_param($stmtCat, 's', $termoCat);
        mysqli_stmt_execute($stmtCat);
        $resCat = mysqli_stmt_get_result($stmtCat);

        if ($cat = mysqli_fetch_assoc($resCat)) {
            $id_categoria = (int)$cat['id_categoria'];

            $sql = "INSERT INTO cicloprodutos (
                        produto, descricao, imagem, id_marca, modelo, preco_venda,
                        qtd_atual, estoque_minimo, id_categoria,
                        modalidade, tamanho_aro, material, cor 
                    ) VALUES (
                        '$nome', '$descricao', '$imagem', '$id_marca', '$modelo', '$preco_venda', 
                        '$qtd_atual', '$estoque_minimo', '$id_categoria', 
                        '$modalidade', '$tamanho_aro', '$material', '$cor'
                    )";

            if (mysqli_query($conn, $sql)) {
                $mensagem = "<p style='color: green; text-align:center; font-weight:bold;'>Produto cadastrado com sucesso!</p>";
            } else {
                $mensagem = "<p style='color: red; text-align:center; font-weight:bold;'>Erro ao cadastrar: " . mysqli_error($conn) . "</p>";
            }
        } else {
            $mensagem = "<p style='color: red; text-align:center; font-weight:bold;'>Erro: Categoria não encontrada no banco de dados.</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Produtos - CicloManos</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, Helvetica, sans-serif; }
body { background: #f7f7f7; color: #222; min-height: 100vh; display: flex; flex-direction: column; }
.form-container { max-width: 850px; margin: 40px auto; background: white; padding: 35px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); width: 92%; }
.form-container h1 { text-align: center; color: #111; margin-bottom: 25px; }
fieldset { border: 2px solid #111; border-radius: 10px; padding: 20px; margin-bottom: 25px; }
legend { color: #111; font-weight: bold; padding: 0 10px; }
label { display: block; margin-top: 12px; color: #333; font-weight: bold; }
input[type="text"] { width: 100%; padding: 12px; margin-top: 5px; border: 1px solid #ccc; border-radius: 8px; font-size: 14px; }
input[type="text"]:focus { outline: none; border-color: #111; }
.botoes { display: flex; justify-content: center; gap: 15px; margin-top: 20px; }
input[type="submit"], input[type="reset"] { padding: 12px 25px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; color: white; transition: 0.3s; }
input[type="submit"] { background: #111; }
input[type="submit"]:hover { background: #333; }
input[type="reset"] { background: #e60023; }
input[type="reset"]:hover { background: #c4001d; }
.btn-voltar { display: inline-block; margin-bottom: 20px; background: #f1f1f1; color: #333; padding: 8px 14px; border-radius: 6px; text-decoration: none; border: 1px solid #ddd; font-weight: bold; }
footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; }
</style>
</head>
<body>

<div class="form-container">
  <a href="painel_funcionario.php" class="btn-voltar">← Voltar ao painel</a>
  <h1>Cadastro de Produto</h1>
  
  <?= $mensagem ?>

  <form action="cadastro.php" method="post">
    <fieldset>
      <legend><b>Informações Básicas do Produto</b></legend>
      <label>Produto:</label>
      <input type="text" name="name" size="40" required>
      <label>Descrição:</label>
      <input type="text" name="descricao" size="40">
      <label>Imagem</label>
      <input type="text" name="imagem" size="40" placeholder="tcc/foto.jpg">
      <label>ID da Marca:</label>
      <input type="text" name="id_marca" size="40" required>
      <label>Modelo:</label>
      <input type="text" name="modelo" size="40">
    </fieldset>

    <fieldset>
      <legend>Preço do Produto</legend>
      <label>Preço de Venda:</label>
      <input type="text" name="preco_venda" size="20" required>
    </fieldset>

    <fieldset>
      <legend>Controle de Estoque</legend>
      <label>Quantidade Atual:</label>
      <input type="text" name="qtd_atual" size="20" required>
      <label>Estoque Mínimo:</label>
      <input type="text" name="estoque_minimo" size="20" required>
    </fieldset>

    <fieldset>
      <legend>Categorização</legend>
      <label>Categoria (Digite o nome, ex: Bicicletas, Peças, Acessórios):</label>
      <input type="text" name="id_categoria" size="40" required placeholder="Nome da Categoria">
      <label>Modalidade:</label>
      <input type="text" name="modalidade" size="20">
    </fieldset>

    <fieldset>
      <legend>Atributos Técnicos</legend>
      <label>Tamanho/Aro:</label>
      <input type="text" name="tamanho_aro" size="20">
      <label>Material:</label>
      <input type="text" name="material" size="20">
      <label>Cor:</label>
      <input type="text" name="cor" size="25">
    </fieldset>

    <div class="botoes">
      <input type="submit" value="Cadastrar">
      <input type="reset" value="Limpar">
    </div>
  </form>
</div>

<footer>© <?= date('Y') ?> CicloManos - Painel do Funcionário</footer>
</body>
</html>