<?php

session_start();

require_once 'config.php';

if (!isset($conn) && isset($conexao)) {
    $conn = $conexao;
}

if (!$conn) {
    die("Erro: Conexão com a base de dados não encontrada. Verifique o seu config.php.");
}

$codigo = '';
$manutencao = null;
$mensagem = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $codigo = trim($_POST['codigo'] ?? '');

    if ($codigo === '' || !ctype_digit($codigo)) {

        $mensagem = 'Informe um código de manutenção válido.';

    } else {

        $sql = 'SELECT
                    m.id_manutencao,
                    m.data_entrada,
                    m.entrega_estimada,
                    m.status,
                    dp.nome AS nome_cliente
                FROM manutencao AS m
                INNER JOIN clientes AS c
                    ON c.id_cliente = m.id_cliente
                INNER JOIN dados_pessoais AS dp
                    ON dp.id_dado = c.id_dado
                WHERE m.id_manutencao = ?';

        $stmt = $conn->prepare($sql);

        if (!$stmt) {

            $mensagem = 'Não foi possível consultar a manutenção. Verifique se a coluna status foi criada no banco.';

        } else {

            $idManutencao = (int) $codigo;

            $stmt->bind_param('i', $idManutencao);
            $stmt->execute();

            $manutencao = $stmt->get_result()->fetch_assoc();

            $stmt->close();

            if (!$manutencao) {
                $mensagem = 'Nenhuma manutenção foi encontrada para esse código.';
            }
        }
    }
}

$etapas = [
    'recebida' => 1,
    'em_analise' => 2,
    'em_manutencao' => 3,
    'pronta' => 4
];

$etapaAtual = $manutencao
    ? ($etapas[$manutencao['status']] ?? 1)
    : 0;

function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

function formatarData($data) {
    return $data ? date('d/m/Y', strtotime($data)) : '-';
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manutenção - CicloManos</title>

    <!-- CSS ÚNICO DO PROJETO -->
    <link rel="stylesheet" href="style.css">

    <!-- CSS ESPECÍFICO DA PÁGINA DE MANUTENÇÃO -->
    <link rel="stylesheet" href="manutencao.css">

    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous"
    >



</head>

<body>


<!-- =====================================================
     TOPO
====================================================== -->

<div class="topo-cinza">

    <a
        href="https://share.google/jYgrtVLyebEBGaqzt"
        target="_blank"
    >
        📍 Localização
    </a>

    <a
        href="https://wa.me/551239163262?text=Ol%C3%A1!%20Vim%20pelo%20site%20da%20CicloManos%20e%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es."
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

<div class="meio-header">

    <!-- LOGO -->

    <a href="index.php">

        <img
            src="tcc/logo.jpg"
            class="logo"
            alt="Logo CicloManos"
        >

    </a>


    <!-- PESQUISA -->

    <div class="busca">

        <input
            type="text"
            placeholder="Digite o que você procura"
        >

        <button>
            Buscar
        </button>

    </div>


    <!-- CONTA E CARRINHO -->

    <div class="usuario">
<?php if (($_SESSION['tipo_usuario'] ?? '') === 'funcionario'): ?>
    <a href="painel_funcionario.php">👤 <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Funcionário', ENT_QUOTES, 'UTF-8') ?></a>
<?php elseif (($_SESSION['tipo_usuario'] ?? '') === 'cliente'): ?>
    <a href="minha_conta.php">👤 Olá, <?= htmlspecialchars($_SESSION['nome_usuario'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?></a>
    <a href="logout.php">Sair</a>
<?php else: ?>
    <a href="login.php">👤 Conta</a>
    <a href="login_funcionario.php">Área do funcionário</a>
<?php endif; ?>
<a href="carrinho.php">🛒 Carrinho</a>
</div>

</div>


<!-- =====================================================
     MENU PRINCIPAL
====================================================== -->

<div class="menu-bar">

    <!-- PRODUTOS -->

    <div class="produtos-menu">

        <button
            class="botao-produtos"
            onclick="abrirProdutos()"
        >
            ☰ Produtos
        </button>


        <!-- CAIXA DROP-DOWN -->

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


    <!-- OUTROS ITENS -->

    <nav>

        <a href="manutencao.php">
            Manutenção
        </a>

        <a href="ofertas.php">
            Ofertas
        </a>

    </nav>

</div>


<!-- =====================================================
     BANNER CENTRAL
====================================================== -->

<div class="banner-central">

    <img
        src="tcc/logo.jpg"
        alt="CicloManos"
    >

</div>


<!-- =====================================================
     ACOMPANHAMENTO DE MANUTENÇÃO
====================================================== -->

<div class="container mb-5">

    <h2 class="text-center fw-bold mb-4">
        Acompanhe sua manutenção
    </h2>


    <div class="manutencao-container">

        <div class="rastreio-card text-center">

            <p class="mb-2">
                Digite o código da sua manutenção:
            </p>


            <form
                method="post"
                action="manutencao.php"
                class="rastreio-form"
            >

                <input
                    type="text"
                    id="codigo"
                    name="codigo"
                    value="<?= e($codigo) ?>"
                    inputmode="numeric"
                    placeholder="Código da manutenção"
                    required
                >

                <button type="submit">
                    Acompanhar
                </button>

            </form>


            <?php if ($mensagem): ?>

                <div class="alert alert-danger mensagem-manutencao">
                    <?= e($mensagem) ?>
                </div>

            <?php endif; ?>


            <?php if ($manutencao): ?>

                <section class="status-manutencao">

                    <h3 class="fw-bold">
                        Status da bicicleta:
                        <?= e(ucwords(str_replace('_', ' ', $manutencao['status']))) ?>
                    </h3>


                    <p class="detalhes-manutencao">

                        Cliente:
                        <strong>
                            <?= e($manutencao['nome_cliente']) ?>
                        </strong>

                        <br>

                        Entrada:
                        <?= formatarData($manutencao['data_entrada']) ?>

                        <br>

                        Entrega estimada:
                        <?= formatarData($manutencao['entrega_estimada']) ?>

                    </p>


                    <div class="etapas-manutencao">

                        <div class="etapa-manutencao <?= $etapaAtual >= 1 ? 'ativa' : '' ?>">
                            Recebida
                        </div>

                        <div class="etapa-manutencao <?= $etapaAtual >= 2 ? 'ativa' : '' ?>">
                            Em análise
                        </div>

                        <div class="etapa-manutencao <?= $etapaAtual >= 3 ? 'ativa' : '' ?>">
                            Em manutenção
                        </div>

                        <div class="etapa-manutencao <?= $etapaAtual >= 4 ? 'ativa' : '' ?>">
                            Pronta
                        </div>

                    </div>

                </section>

            <?php endif; ?>

        </div>

    </div>

</div>


<!-- =====================================================
     RODAPÉ
====================================================== -->

<footer class="mt-5 text-center p-3 border-top">

    <p class="mb-0">
        © <?= date('Y') ?> CicloManos - Todos os direitos reservados.
    </p>

</footer>


<!-- =====================================================
     JAVASCRIPT
====================================================== -->

<script>

function abrirProdutos() {

    const caixa =
        document.getElementById("caixa-produtos");

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
