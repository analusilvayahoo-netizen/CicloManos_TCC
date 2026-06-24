<?php

include 'config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM cicloprodutos WHERE id = $id";

$result = mysqli_query($conn,$sql);

if(mysqli_num_rows($result)==0){
    die("Produto não encontrado.");
}

$produto = mysqli_fetch_assoc($result);

?>

<html>
<head>
<meta charset="UTF-8">
<title> Produtos </title>
</head>

<center>

<div class="container mt-5">

<div class="row">

<div class="col-md-6">

<img src="<?= $produto['imagem']; ?>"
     class="img-fluid">

</div>

<div class="col-md-6">

<h1><?= $produto['produto']; ?></h1>

<h3 class="text-danger">
R$ <?= $produto['preco_venda']; ?>
</h3>

<p>
<?= $produto['descricao']; ?>
</p>

<form action="carrinho.php" method="post">

<input type="hidden"
       name="id"
       value="<?= $produto['id']; ?>">

<button class="btn btn-success">
Adicionar ao Carrinho
</button>

</form>

</div>

</div>

</div>

</center>

</body>
</html>