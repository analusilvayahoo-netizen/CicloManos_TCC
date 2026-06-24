<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>CicloManos</title>

<link rel="stylesheet" href="Desing.css">

</head>

<body>

<div class="topo">
  <a href="#">📍 Rastreie seu pedido</a>
  <a href="#">💬 Fale conosco</a>
  <span>📱 WhatsApp: (12) 99999-0000</span>
  <span>📞 Telefone: (12) 3721-0000</span>
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

  <a href="departamentos.html" class="departamentos">
    ☰ Departamentos
  </a>

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

<h2 class="titulo">LANÇAMENTOS</h2>

<div class="produtos">

<?php

$sql = "SELECT * FROM cicloprodutos";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){

echo "

<a href='#'>

<div class='card'>

<img src='".$row['imagem']."' alt='Produto'>

<h3>".$row['produto']."</h3>

<p class='preco'>
R$ ".number_format($row['preco_venda'],2,',','.')."
</p>

</div>

</a>

";

}

}else{

echo "<h3>Nenhum produto encontrado.</h3>";

}

mysqli_close($conn);

?>

</div>

<footer>
  <p>© 2026 CicloManos - Todos os direitos reservados</p>
</footer>

</body>
</html>