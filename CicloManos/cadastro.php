<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title> CicloManos </title>

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

/* TOPO */
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

/* HEADER */
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

/* MENU */
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
</head>


<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $conn = mysqli_connect("localhost", "root", "", "ciclomanos_db");

    
    if ($conn) {
        
       
        $nome          = mysqli_real_escape_string($conn, $_POST['nome_produto']);
        $marca         = mysqli_real_escape_string($conn, $_POST['marca']);
        $modelo        = mysqli_real_escape_string($conn, $_POST['modelo']);
        $preco_custo   = mysqli_real_escape_string($conn, $_POST['preco_custo']);
        $preco_venda   = mysqli_real_escape_string($conn, $_POST['preco_venda']);
        $margem_lucro  = mysqli_real_escape_string($conn, $_POST['margem_lucro']);
        $qtd_atual     = mysqli_real_escape_string($conn, $_POST['qtd_atual']);
        $estoque_min   = mysqli_real_escape_string($conn, $_POST['estoque_minimo']);
        $cat_principal = mysqli_real_escape_string($conn, $_POST['categoria_principal']);
        $subcategoria  = mysqli_real_escape_string($conn, $_POST['subcategoria']);
        $modalidade    = mysqli_real_escape_string($conn, $_POST['modalidade']);
        $tamanho_aro   = mysqli_real_escape_string($conn, $_POST['tamanho_aro']);
        $material      = mysqli_real_escape_string($conn, $_POST['material']);
        $cor           = mysqli_real_escape_string($conn, $_POST['cor']);
        $condicao      = mysqli_real_escape_string($conn, $_POST['condicao']);

        
        $sql = "INSERT INTO cicloprodutos (
                    produto, marca, modelo, preco_custo, preco_venda, margem_lucro, 
                    qtd_atual, estoque_minimo, categoria_principal, subcategoria, 
                    modalidade, tamanho_aro, material, cor, condicao
                ) VALUES (
                    '$nome', '$marca', '$modelo', '$preco_custo', '$preco_venda', '$margem_lucro', 
                    '$qtd_atual', '$estoque_min', '$cat_principal', '$subcategoria', 
                    '$modalidade', '$tamanho_aro', '$material', '$cor', '$condicao'
                )";

 
        if (mysqli_query($conn, $sql)) {
            echo "<p style='color: green;'> Produto cadastrado com sucesso! </p>";
        } else {
            echo "<p style='color: red;'> Erro ao cadastrar: " . mysqli_error($conn) . "</p>";
        }


        mysqli_close($conn);
    }
}
?>

</body>
</html>