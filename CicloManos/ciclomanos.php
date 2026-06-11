<?php

include 'config.php';

?>

<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>CicloManos</title>

<style>

*{
  margin:0;
  padding:0;
  box-sizing:border-box;
}

body{
  font-family: Arial;
  background:#f5f5f5;
}

a{
  text-decoration:none;
  color:inherit;
}


.topo{
  background:#f5f5f5;
  display:flex;
  justify-content:space-around;
  padding:8px;
  font-size:12px;
}

.topo a:hover{
  color:#e60000;
}


.meio-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:15px 40px;
  background:white;
}

.logo{
  height:120px;
}

.busca{
  display:flex;
  width:40%;
}

.busca input{
  width:100%;
  padding:10px;
  border:1px solid #ccc;
  border-radius:5px 0 0 5px;
}

.busca button{
  padding:10px 20px;
  border:none;
  background:#4A86B8;
  color:white;
  border-radius:0 5px 5px 0;
  cursor:pointer;
}

.usuario a{
  display:block;
  margin:3px 0;
}

.usuario a:hover{
  color:#4A86B8;
}


.menu{
  background:#4A86B8;
  color:white;
  display:flex;
  align-items:center;
  padding:10px 40px;
  gap:20px;
}

.menu nav a{
  margin:0 10px;
}

.menu nav a:hover{
  text-decoration:underline;
}


.departamentos{
  background:#3f719b;
  color:white;
  padding:10px 15px;
  border-radius:5px;
}


.banner{
  height:400px;
  display:flex;
  justify-content:center;
  align-items:center;
  background:white;
}

.banner img{
  max-height:100%;
  max-width:100%;
}


.titulo{
  text-align:center;
  margin:30px;
}

.produtos{
  display:grid;
  grid-template-columns:repeat(auto-fit, minmax(220px,1fr));
  gap:25px;
  padding:20px 40px;
}

.card{
  background:white;
  border-radius:10px;
  padding:15px;
  text-align:center;
  box-shadow:0 4px 10px rgba(0,0,0,0.1);
  transition:0.3s;
  height:300px;
  display:flex;
  flex-direction:column;
  justify-content:space-between;
}

.card:hover{
  transform:translateY(-5px);
}

.card img{
  width:100%;
  height:140px;
  object-fit:contain;
}

.preco{
  color:#e60023;
  font-weight:bold;
  margin-top:10px;
}


footer{
  background:#111;
  color:white;
  text-align:center;
  padding:20px;
  margin-top:40px;
}

</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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