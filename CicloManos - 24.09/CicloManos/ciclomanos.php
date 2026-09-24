<?php

session_start();

include 'config.php';

/*
 * Compatibilidade com diferentes nomes
 * usados no arquivo config.php.
 */
if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

if (!isset($conn) || !$conn) {
    die("Erro: conexão com a base de dados não encontrada. Verifique o seu config.php.");
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CicloManos</title>

    <!-- CSS EXCLUSIVO DO CICLOMANOS -->
    <link rel="stylesheet" href="ciclomanos.css">

</head>

<body>


<!-- =====================================================
     BARRA SUPERIOR
====================================================== -->

<div class="topo-cinza">

    <a
        href="https://share.google/jYgrtVLyebEBGaqzt"
        target="_blank"
    >
        📍 Localização
    </a>


    <a
        href="https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es"
        target="_blank"
    >
        💬 Fale conosco
    </a>


    <a
        href="https://wa.me/551239163262"
        target="_blank"
    >
        📱 WhatsApp: (12) 3916-3262
    </a>


    <span>
        📞 Telefone: (12) 3916-3262
    </span>

</div>


<!-- =====================================================
     CABEÇALHO
====================================================== -->

<header class="meio-header">


    <!-- LOGO -->

    <a href="ciclomanos.php">

        <img
            src="tcc/logo.jpg"
            class="logo"
            alt="Logo CicloManos"
        >

    </a>


    <!-- BUSCA -->

    <div class="busca">

        <input
            type="text"
            placeholder="Digite o que você procura"
        >

        <button type="button">
            Buscar
        </button>

    </div>


    <!-- CONTA E CARRINHO -->

    <div class="usuario">

        <a href="login.php">
            👤 Conta
        </a>

        <a href="carrinho.php">
            🛒 Carrinho
        </a>

    </div>

</header>


<!-- =====================================================
     MENU PRINCIPAL
====================================================== -->

<div class="menu-bar">


    <!-- PRODUTOS -->

    <div class="produtos-menu">

        <button
            type="button"
            class="botao-produtos"
            onclick="abrirProdutos()"
        >
            ☰ Produtos
        </button>


        <!-- MENU DROPDOWN -->

        <div
            id="caixa-produtos"
            class="caixa-produtos"
        >

            <a href="acessorios.php">
                Acessórios
            </a>

            <a href="pecas.php">
                Peças
            </a>

            <a href="bicicletas.php">
                Bicicletas
            </a>

        </div>

    </div>


    <!-- OUTROS LINKS -->

    <nav>

        <a href="manutencao.php">
            Manutenção
        </a>

        <a href="ofertas.html">
            Ofertas
        </a>

    </nav>

</div>


<!-- =====================================================
     BANNER
====================================================== -->

<section class="banner-central">

    <img
        src="tcc/logo.jpg"
        alt="CicloManos"
    >

</section>


<!-- =====================================================
     PRODUTOS
====================================================== -->

<main class="container-produtos">

    <h2>
        Nossa Vitrine de Produtos
    </h2>


    <div class="produtos">


        <?php

        /*
         * Busca os 16 produtos mais recentemente
         * cadastrados no banco de dados.
         */

        $sql = "
            SELECT *
            FROM cicloprodutos
            ORDER BY id DESC
            LIMIT 16
        ";


        $result = mysqli_query($conn, $sql);


        /*
         * Verifica se houve erro na consulta.
         */

        if (!$result) {

            echo '

                <div class="mensagem erro">

                    Erro ao buscar os produtos:
                    ' . htmlspecialchars(mysqli_error($conn)) . '

                </div>

            ';

        } else {


            /*
             * Verifica se existem produtos.
             */

            if (mysqli_num_rows($result) > 0) {


                /*
                 * Percorre os produtos.
                 */

                while ($row = mysqli_fetch_assoc($result)) {

                    ?>


                    <div class="card-produto">


                        <!-- IMAGEM -->

                        <a
                            href="produtos.php?id=<?= $row['id'] ?>"
                            class="link-produto"
                        >

                            <img
                                src="<?= htmlspecialchars($row['imagem']) ?>"
                                alt="<?= htmlspecialchars($row['produto']) ?>"
                            >


                            <!-- NOME -->

                            <div class="titulo-produto">

                                <?= htmlspecialchars($row['produto']) ?>

                            </div>


                            <!-- PREÇO -->

                            <div class="preco-produto">

                                R$
                                <?= number_format(
                                    $row['preco_venda'],
                                    2,
                                    ',',
                                    '.'
                                ) ?>

                            </div>

                        </a>


                        <!-- BOTÃO CARRINHO -->

                        <a
                            href="carrinho.php?acao=add&id=<?= $row['id'] ?>"
                            class="btn-carrinho"
                        >

                            🛒 Colocar no carrinho

                        </a>

                    </div>


                    <?php

                }


            } else {

                ?>

                <div class="mensagem aviso">

                    Nenhum produto encontrado.

                </div>

                <?php

            }

        }

        ?>

    </div>

</main>


<!-- =====================================================
     RODAPÉ
====================================================== -->

<footer>

    <p>
        © <?= date('Y') ?> CicloManos - Todos os direitos reservados.
    </p>

</footer>


<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script>

function abrirProdutos() {

    const caixa = document.getElementById("caixa-produtos");

    caixa.classList.toggle("aberto");

}


document.addEventListener("click", function(event) {

    const produtosMenu =
        document.querySelector(".produtos-menu");

    const caixa =
        document.getElementById("caixa-produtos");


    if (
        produtosMenu &&
        !produtosMenu.contains(event.target)
    ) {

        caixa.classList.remove("aberto");

    }

});

</script>


</body>

</html>

<?php

/*
 * Fecha a conexão com o banco.
 */

mysqli_close($conn);

?>
