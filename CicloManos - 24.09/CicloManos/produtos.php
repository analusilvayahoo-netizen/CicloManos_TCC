<?php
include 'config.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>CicloManos</title>

<link rel="stylesheet" href="desing.css">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

</head>

<body>
<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM cicloprodutos WHERE id = $id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    die("Produto não encontrado.");
}

$produto = mysqli_fetch_assoc($result);
?>


  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .topo {
      background: #f5f5f5;
      display: flex;
      justify-content: space-around;
      padding: 8px;
      font-size: 12px;
    }

    .topo a:hover {
      color: #e60000;
    }

    .meio-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 40px;
      background: white;
    }

    .logo {
      height: 120px;
    }

    .busca {
      display: flex;
      width: 40%;
    }

    .busca input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px 0 0 5px;
    }

    .busca button {
      padding: 10px 20px;
      border: none;
      background: #4A86B8;
      color: white;
      border-radius: 0 5px 5px 0;
      cursor: pointer;
    }

    .usuario a {
      display: block;
      margin: 3px 0;
    }

    .usuario a:hover {
      color: #4A86B8;
    }

    .menu {
      background: #4A86B8;
      color: white;
      display: flex;
      align-items: center;
      padding: 10px 40px;
      gap: 20px;
    }

    .menu nav a {
      margin: 0 10px;
    }

    .menu nav a:hover {
      text-decoration: underline;
    }

    .departamentos {
      background: #3f719b;
      color: white;
      padding: 10px 15px;
      border-radius: 5px;
    }

    .banner {
      height: 400px;
      display: flex;
      justify-content: center;
      align-items: center;
      background: white;
    }

    .banner img {
      max-height: 100%;
      max-width: 100%;
    }

    .titulo {
      text-align: center;
      margin: 30px;
    }

    .produtos {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 25px;
      padding: 20px 40px;
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: 0.3s;
      height: 300px;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card img {
      width: 100%;
      height: 140px;
      object-fit: contain;
    }

    .preco {
      color: #e60023;
      font-weight: bold;
      margin-top: 10px;
    }

    footer {
      background: #111;
      color: white;
      text-align: center;
      padding: 20px;
      margin-top: 40px;
    }
  </style>
</head>
<body>

  <div class="topo">
    <a href="#">📍 Rastreie seu pedido</a>
    <a href="#">💬 Fale conosco</a>
    <span>📱 WhatsApp: (12) 99999-0000</span>
    <span>📞 Telefone: (12) 3721-0000</span>
  </div>

<br>
 <br>

 <center>

  <div class="container mt-5">
    <div class="row align-items-center">
      
      <div class="col-md-6 text-center">
        <img src="<?= htmlspecialchars($produto['imagem']); ?>" class="img-fluid rounded" alt="<?= htmlspecialchars($produto['produto']); ?>">
      </div>

      <div class="col-md-6">
        <h1 class="mb-3"><?= htmlspecialchars($produto['produto']); ?></h1>

<br>        
        
        <h3 class="text-danger mb-3">
          R$ <?= number_format($produto['preco_venda'], 2, ',', '.'); ?>
        </h3>

        <p class="text-muted">
          <?= nl2br(htmlspecialchars($produto['descricao'])); ?>
        </p>

<br>        

        <form action="carrinho.php" method="post" class="mt-4">
          <input type="hidden" name="id" value="<?= (int)$produto['id']; ?>">
          <button class="btn btn-success btn-lg w-100">
            🛒 Adicionar ao Carrinho
          </button>
        </form>
      </div>
</center>

    </div>
  </div>

  <footer>
    <p class="m-0">&copy; 2026 CicloManos. Todos os direitos reservados.</p>
  </footer>

  <script src="https://jsdelivr.net"></script>
</body>
</html>