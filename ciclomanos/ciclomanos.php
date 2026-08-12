<?php

include 'config.php';

?>

<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title> CicloManos </title>


<link rel="stylesheet" href="desing.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">


</head>

<body>

<div class="topo">
  <a href="#">📍 Rastreie seu pedido</a>
  <a href="#">💬 Fale conosco</a>
  <span>📱 WhatsApp: (12) 3916-3262</span>
  <span>📞 Telefone: (12) 3916-3262</span>
</div>

<div class="meio-header">

  <a href="ciclomanos.php">
    <img src="tcc/logo.jpg" class="logo">
  </a>

  <div class="busca">
    <input type="text" placeholder="Digite o que você procura">
    <button>Buscar</button>
  </div>

  <div class="usuario">
    <a href="#">👤 Conta</a>
    <a href="#">🛒 Carrinho</a>
  </div>

</div>

<div class="menu">

  <a href="departamentos.html" class="departamentos">☰ Departamentos</a>

  <nav>
    <a href="acessorios.html">Acessórios</a>
    <a href="bicicletas.html">Bicicletas</a>
    <a href="pecas.html">Peças</a>
    <a href="manutencao.html">Manutenção</a>
    <a href="ofertas.html">Ofertas</a>
  </nav>

</div>

<div class="banner">
  <img src="tcc/logo.jpg">
</div>


<div class="produtos">


</div>


 <div class="container mt-5">

 <h1 class="text-center mb-4"> Nossa Vitrine de Produtos </h1>
 <?php




$sql = "SELECT * FROM cicloprodutos";


$result = mysqli_query($conn, $sql);


if (mysqli_num_rows($result) > 0) {

echo "<div class='row'>";



while($row = mysqli_fetch_assoc($result)){

echo "

<div class='col-12 col-md-6 col-lg-3 mb-4'>

<a href='produtos.php?id=".$row['id']."' style='text-decoration:none;color:inherit;'>

<div class='card h-100 shadow-sm'>

<img src='".$row['imagem']."'
     class='card-img-top'
     alt='Imagem do produto'>

<div class='card-body d-flex flex-column'>

<h5 class='card-title'>".$row['produto']."</h5>

<p class='card-text'>".$row['descricao']."</p>

<div class='mt-auto'>

<button class='btn btn-primary w-100'>
R$ ".$row['preco_venda']."
</button>

</div>

</div>

</div>

</a>

</div>

";

}


echo "</div>"; 

 } else {

echo "<p class='alert alert-warning'>Nenhum produto encontrado.</p>";

 }


 mysqli_close($conn);

?>

</div>


<footer>
  <p> © 2026 CicloManos - Todos os direitos reservados. </p>
</footer>

</body>
</html>