<?php

session_start();

include 'config.php';



if (!isset($_SESSION['carrinho'])) {
    $_SESSION['carrinho'] = [];
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar'])) {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );
    
    if ($id !== false && $id !== null) {

        $sql = "
            SELECT
                id,
                produto,
                descricao,
                imagem,
                preco_venda
            FROM cicloprodutos
            WHERE id = ?
        ";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "i",
            $id
        );

        mysqli_stmt_execute($stmt);

        $resultado = mysqli_stmt_get_result($stmt);

        $produto = mysqli_fetch_assoc($resultado);


        if ($produto) {

            if (isset($_SESSION['carrinho'][$id])) {

                $_SESSION['carrinho'][$id]['quantidade']++;

            } else {

                $_SESSION['carrinho'][$id] = [

                    'id' => (int)$produto['id'],

                    'produto' => $produto['produto'],

                    'descricao' => $produto['descricao'],

                    'imagem' => $produto['imagem'],

                    'preco' => (float)$produto['preco_venda'],

                    'quantidade' => 1

                ];
            }
        }

        mysqli_stmt_close($stmt);
    }


    header('Location: carrinho.php');

    exit;
}



if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['aumentar'])
) {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (
        $id !== false &&
        $id !== null &&
        isset($_SESSION['carrinho'][$id])
    ) {

        $_SESSION['carrinho'][$id]['quantidade']++;
    }


    header('Location: carrinho.php');

    exit;
}



if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['diminuir'])
) {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (
        $id !== false &&
        $id !== null &&
        isset($_SESSION['carrinho'][$id])
    ) {

        $_SESSION['carrinho'][$id]['quantidade']--;


        if ($_SESSION['carrinho'][$id]['quantidade'] <= 0) {

            unset($_SESSION['carrinho'][$id]);
        }
    }


    header('Location: carrinho.php');

    exit;
}



if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['remover'])
) {

    $id = filter_input(
        INPUT_POST,
        'id',
        FILTER_VALIDATE_INT
    );

    if (
        $id !== false &&
        $id !== null
    ) {

        unset($_SESSION['carrinho'][$id]);
    }


    header('Location: carrinho.php');

    exit;
}



if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['limpar'])
) {

    $_SESSION['carrinho'] = [];

    header('Location: carrinho.php');

    exit;
}



$total = 0;

foreach ($_SESSION['carrinho'] as $produto) {

    $total +=
        $produto['preco']
        *
        $produto['quantidade'];
}


?>

<!DOCTYPE html>

<html lang="pt-br">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Carrinho - CicloManos</title>

<link
    rel="stylesheet"
    href="carrinho.css"
>

</head>


<body>


<div class="topo">

<a href="#">
📍 Rastreie seu pedido
</a>

<a href="#">
💬 Fale conosco
</a>

<span>
📱 WhatsApp: (12) 3916-3262
</span>

<span>
📞 Telefone: (12) 3916-3262
</span>

</div>


<div class="meio-header">

<a href="ciclomanos.php">

<img
    src="tcc/logo.jpg"
    class="logo"
    alt="CicloManos"
>

</a>


<div class="usuario">

<a href="login.php">
👤 Conta
</a>

<a href="carrinho.php">
🛒 Carrinho
</a>

</div>

</div>


<div class="menu">

<a
    href="departamentos.html"
    class="departamentos"
>
☰ Departamentos
</a>


<nav>

<a href="acessorios.html">
Acessórios
</a>

<a href="bicicletas.html">
Bicicletas
</a>

<a href="pecas.html">
Peças
</a>

<a href="manutencao.php">
Manutenção
</a>

<a href="ofertas.html">
Ofertas
</a>

</nav>

</div>


<main class="carrinho-area">

<div class="carrinho-container">


<h1>
🛒 Meu Carrinho
</h1>


<?php if (empty($_SESSION['carrinho'])): ?>


<div class="carrinho-vazio">

<h2>
Seu carrinho está vazio
</h2>

<p>
Adicione produtos para começar sua compra.
</p>

<a
    href="ciclomanos.php"
    class="botao-comprar"
>
Continuar comprando
</a>

</div>


<?php else: ?>


<div class="conteudo-carrinho">


<div class="produtos-carrinho">


<?php foreach ($_SESSION['carrinho'] as $produto): ?>


<div class="produto-carrinho">


<div class="produto-imagem">

<img
    src="<?php echo htmlspecialchars($produto['imagem']); ?>"
    alt="<?php echo htmlspecialchars($produto['produto']); ?>"
>

</div>


<div class="produto-info">

<h2>

<?php
echo htmlspecialchars(
    $produto['produto']
);
?>

</h2>


<p class="preco">

R$

<?php

echo number_format(
    $produto['preco'],
    2,
    ',',
    '.'
);

?>

</p>


<div class="produto-acoes">


<div class="quantidade">


<form method="POST">

<input
    type="hidden"
    name="id"
    value="<?php echo $produto['id']; ?>"
>

<button
    type="submit"
    name="diminuir"
>
−
</button>

</form>


<span>

<?php
echo $produto['quantidade'];
?>

</span>


<form method="POST">

<input
    type="hidden"
    name="id"
    value="<?php echo $produto['id']; ?>"
>

<button
    type="submit"
    name="aumentar"
>
+
</button>

</form>


</div>


<form method="POST">

<input
    type="hidden"
    name="id"
    value="<?php echo $produto['id']; ?>"
>

<button
    type="submit"
    name="remover"
    class="remover"
>
🗑 Remover
</button>

</form>


</div>

</div>


<div class="produto-total">

R$

<?php

$subtotalProduto =
    $produto['preco']
    *
    $produto['quantidade'];

echo number_format(
    $subtotalProduto,
    2,
    ',',
    '.'
);

?>

</div>


</div>


<?php endforeach; ?>


<form
    method="POST"
    class="limpar-form"
>

<button
    type="submit"
    name="limpar"
    class="limpar"
>
Esvaziar carrinho
</button>

</form>


</div>


<aside class="resumo">

<h2>
Resumo da compra
</h2>


<div class="resumo-linha">

<span>
Subtotal
</span>

<strong>

R$

<?php

echo number_format(
    $total,
    2,
    ',',
    '.'
);

?>

</strong>

</div>


<div class="resumo-linha">

<span>
Frete
</span>

<strong>
Grátis
</strong>

</div>


<hr>


<div class="resumo-total">

<span>
Total
</span>

<strong>

R$

<?php

echo number_format(
    $total,
    2,
    ',',
    '.'
);

?>

</strong>

</div>


<button
    type="button"
    class="finalizar"
>
Finalizar compra
</button>


</aside>


</div>


<?php endif; ?>


</div>

</main>


<footer>

<p>
© 2026 CicloManos - Todos os direitos reservados.
</p>

</footer>


</body>

</html>