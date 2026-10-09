<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();

/* =========================================================
   FUNÇÕES AUXILIARES
========================================================= */

function e(?string $valor): string
{
    return htmlspecialchars(
        (string)$valor,
        ENT_QUOTES,
        'UTF-8'
    );
}


function cpfValido(string $cpf): bool
{
    $cpf = preg_replace('/\D/', '', $cpf) ?? '';

    if (strlen($cpf) !== 11) {
        return false;
    }

    if (preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($p = 9; $p < 11; $p++) {

        $soma = 0;

        for ($i = 0; $i < $p; $i++) {
            $soma += (int)$cpf[$i] * (($p + 1) - $i);
        }

        $digito = (($soma * 10) % 11) % 10;

        if ($digito !== (int)$cpf[$p]) {
            return false;
        }
    }

    return true;
}


/* =========================================================
   CONEXÃO COM O BANCO
========================================================= */

try {

    $pdo = new PDO(
        'mysql:host=localhost;dbname=ciclomanos;charset=utf8mb4',
        'root',
        '',
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );


    /* Buscar bairros */

    $bairros = $pdo->query(
        'SELECT id_bairro, nome_bairro
         FROM bairros
         ORDER BY nome_bairro'
    )->fetchAll();

    $quantidadeFuncionarios = (int) $pdo
        ->query('SELECT COUNT(*) FROM funcionarios')
        ->fetchColumn();

} catch (PDOException $erro) {

    die(
        '<h2>Erro ao conectar ao banco de dados</h2>' .
        '<p>Verifique se o MySQL está ligado e se o banco ' .
        '<strong>ciclomanos</strong> existe.</p>' .
        '<p>Erro: ' . e($erro->getMessage()) . '</p>'
    );
}

// Permite cadastrar o primeiro funcionário somente no próprio computador.
$primeiroCadastro = ($quantidadeFuncionarios === 0);
$acessoLocal = in_array(
    $_SERVER['REMOTE_ADDR'] ?? '',
    ['127.0.0.1', '::1'],
    true
);
$funcionarioLogado = (($_SESSION['tipo_usuario'] ?? '') === 'funcionario');

if (!$funcionarioLogado && !($primeiroCadastro && $acessoLocal)) {
    header('Location: login_funcionario.php');
    exit;
}


/* =========================================================
   VARIÁVEIS DO FORMULÁRIO
========================================================= */

$mensagem = '';
$sucesso = false;

$campos = [

    'nome' => '',
    'email' => '',
    'cpf' => '',
    'rua' => '',
    'cep' => '',
    'bairro' => '',
    'cargo' => '',
    'data_admissao' => date('Y-m-d')

];


/* =========================================================
   PROCESSAMENTO DO FORMULÁRIO
========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($campos as $campo => $valor) {

        $campos[$campo] = trim(
            (string)($_POST[$campo] ?? '')
        );
    }


    $nome = $campos['nome'];

    $email = strtolower(
        $campos['email']
    );

    $cpf = preg_replace(
        '/\D/',
        '',
        $campos['cpf']
    ) ?? '';

    $rua = $campos['rua'];

    $cep = preg_replace(
        '/\D/',
        '',
        $campos['cep']
    ) ?? '';

    $bairroId = filter_var(
        $campos['bairro'],
        FILTER_VALIDATE_INT
    );

    $cargo = $campos['cargo'];

    $dataAdmissao = $campos['data_admissao'];

    $senha = (string)(
        $_POST['senha'] ?? ''
    );

    $confirmarSenha = (string)(
        $_POST['confirmar_senha'] ?? ''
    );


    /* IDs de bairros válidos */

    $bairrosValidos = array_map(
        static fn(array $bairro): int =>
            (int)$bairro['id_bairro'],
        $bairros
    );


    /* =====================================================
       VALIDAÇÕES
    ===================================================== */

    if (mb_strlen($nome) < 3) {

        $mensagem =
            'Digite o nome completo do funcionário.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $mensagem =
            'Digite um e-mail válido.';

    } elseif (!str_ends_with($email, '@ciclomanos.com')) {

        $mensagem =
            'O e-mail do funcionário deve terminar com @ciclomanos.com.';

    } elseif (!cpfValido($cpf)) {

        $mensagem =
            'Digite um CPF válido.';

    } elseif (mb_strlen($rua) < 3) {

        $mensagem =
            'Digite uma rua válida.';

    } elseif (strlen($cep) !== 8) {

        $mensagem =
            'Digite um CEP com 8 números.';

    } elseif (
        $bairroId === false ||
        !in_array(
            $bairroId,
            $bairrosValidos,
            true
        )
    ) {

        $mensagem =
            'Selecione um bairro válido.';

    } elseif (mb_strlen($cargo) < 2) {

        $mensagem =
            'Digite o cargo do funcionário.';

    } elseif ($dataAdmissao === '') {

        $mensagem =
            'Informe a data de admissão.';

    } elseif (strlen($senha) < 8) {

        $mensagem =
            'A senha deve ter pelo menos 8 caracteres.';

    } elseif ($senha !== $confirmarSenha) {

        $mensagem =
            'As senhas não coincidem.';

    } else {

        try {

            $pdo->beginTransaction();


            /* =================================================
               VERIFICAR E-MAIL OU CPF
            ================================================= */

            $q = $pdo->prepare(
                'SELECT id_dado
                 FROM dados_pessoais
                 WHERE email = :email
                    OR cpf = :cpf
                 LIMIT 1'
            );

            $q->execute([
                'email' => $email,
                'cpf' => $cpf
            ]);


            if ($q->fetch()) {

                throw new RuntimeException(
                    'Já existe um cadastro com este e-mail ou CPF.'
                );
            }


            /* =================================================
               CADASTRAR ENDEREÇO
            ================================================= */

            $q = $pdo->prepare(
                'INSERT INTO enderecos
                    (rua, cep, id_bairro)
                 VALUES
                    (:rua, :cep, :bairro)'
            );

            $q->execute([

                'rua' => $rua,

                'cep' => $cep,

                'bairro' => $bairroId

            ]);

            $enderecoId = (int)$pdo->lastInsertId();


            /* =================================================
               CADASTRAR DADOS PESSOAIS
            ================================================= */

            $q = $pdo->prepare(
                'INSERT INTO dados_pessoais
                    (cpf, nome, email, id_endereco)
                 VALUES
                    (:cpf, :nome, :email, :endereco)'
            );

            $q->execute([

                'cpf' => $cpf,

                'nome' => $nome,

                'email' => $email,

                'endereco' => $enderecoId

            ]);

            $dadoId = (int)$pdo->lastInsertId();


            /* =================================================
               CADASTRAR USUÁRIO
            ================================================= */

            $q = $pdo->prepare(
                'INSERT INTO usuarios
                    (id_dado, senha_hash, tipo)
                 VALUES
                    (:dado, :senha, :tipo)'
            );

            $q->execute([

                'dado' => $dadoId,

                'senha' =>
                    password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    ),

                'tipo' => 'funcionario'

            ]);


            /* =================================================
               CADASTRAR FUNCIONÁRIO
            ================================================= */

            $q = $pdo->prepare(
                'INSERT INTO funcionarios
                    (id_dado, cargo, data_admissao)
                 VALUES
                    (:dado, :cargo, :data_admissao)'
            );

            $q->execute([

                'dado' => $dadoId,

                'cargo' => $cargo,

                'data_admissao' => $dataAdmissao

            ]);


            /* =================================================
               FINALIZAR TRANSAÇÃO
            ================================================= */

            $pdo->commit();

            $mensagem =
                'Funcionário cadastrado com sucesso! ' .
                'Ele já pode entrar pelo login usando o e-mail @ciclomanos.com.';

            $sucesso = true;


            /* Limpar formulário */

            $campos = [

                'nome' => '',
                'email' => '',
                'cpf' => '',
                'rua' => '',
                'cep' => '',
                'bairro' => '',
                'cargo' => '',
                'data_admissao' => date('Y-m-d')

            ];


        } catch (RuntimeException $erro) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $mensagem = $erro->getMessage();


        } catch (PDOException $erro) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $mensagem =
                'Não foi possível cadastrar o funcionário. ' .
                'Verifique as tabelas do banco de dados.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Cadastrar Funcionário - CicloManos
    </title>

    <link
        rel="stylesheet"
        href="funcionario.css?v=4"
    >

</head>


<body>


<header>

    <a
        href="painel_funcionario.php"
        class="logo"
    >
        🚲 CicloManos
    </a>


    <div class="funcionario-logado">

        <div class="funcionario-icone">
            👤
        </div>


        <div class="dados-funcionario">

            <strong>
                <?= e(
                    $_SESSION['nome_usuario']
                    ?? 'Funcionário'
                ) ?>
            </strong>

            <span>
                <?= e(
                    $_SESSION['cargo_funcionario']
                    ?? 'Funcionário'
                ) ?>
            </span>

        </div>

    </div>

</header>


<main class="container">


    <div class="topo">

        <h1>
            Cadastrar Funcionário
        </h1>

        <p>
            Cadastre um novo funcionário para ter
            acesso ao painel administrativo.
        </p>

    </div>


    <?php if ($mensagem !== ''): ?>

        <div
            class="mensagem <?= $sucesso ? 'sucesso' : 'erro' ?>"
        >

            <?= e($mensagem) ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        action="cadastro_funcionario.php"
        class="formulario"
    >


        <div class="grid">


            <!-- NOME -->

            <div class="campo campo-completo">

                <label for="nome">
                    Nome completo
                </label>

                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= e($campos['nome']) ?>"
                    placeholder="Nome completo do funcionário"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="campo">

                <label for="email">
                    E-mail profissional
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($campos['email']) ?>"
                    placeholder="nome@ciclomanos.com"
                    required
                >

                <span class="ajuda">
                    Obrigatoriamente deve terminar em
                    @ciclomanos.com.
                </span>

            </div>


            <!-- CPF -->

            <div class="campo">

                <label for="cpf">
                    CPF
                </label>

                <input
                    type="text"
                    id="cpf"
                    name="cpf"
                    value="<?= e($campos['cpf']) ?>"
                    placeholder="000.000.000-00"
                    maxlength="14"
                    required
                >

            </div>


            <!-- CARGO -->

            <div class="campo">

                <label for="cargo">
                    Cargo
                </label>

                <input
                    type="text"
                    id="cargo"
                    name="cargo"
                    value="<?= e($campos['cargo']) ?>"
                    placeholder="Ex.: Mecânico"
                    maxlength="50"
                    required
                >

            </div>


            <!-- DATA -->

            <div class="campo">

                <label for="data_admissao">
                    Data de admissão
                </label>

                <input
                    type="date"
                    id="data_admissao"
                    name="data_admissao"
                    value="<?= e($campos['data_admissao']) ?>"
                    required
                >

            </div>


            <!-- RUA -->

            <div class="campo campo-completo">

                <label for="rua">
                    Rua
                </label>

                <input
                    type="text"
                    id="rua"
                    name="rua"
                    value="<?= e($campos['rua']) ?>"
                    placeholder="Nome da rua"
                    required
                >

            </div>


            <!-- CEP -->

            <div class="campo">

                <label for="cep">
                    CEP
                </label>

                <input
                    type="text"
                    id="cep"
                    name="cep"
                    value="<?= e($campos['cep']) ?>"
                    placeholder="00000-000"
                    maxlength="9"
                    required
                >

            </div>


            <!-- BAIRRO -->

            <div class="campo">

                <label for="bairro">
                    Bairro
                </label>

                <select
                    id="bairro"
                    name="bairro"
                    required
                >

                    <option value="">
                        Selecione o bairro
                    </option>


                    <?php foreach ($bairros as $bairro): ?>

                        <option
                            value="<?= (int)$bairro['id_bairro'] ?>"
                            <?= (
                                (string)$campos['bairro']
                                ===
                                (string)$bairro['id_bairro']
                            )
                                ? 'selected'
                                : ''
                            ?>
                        >

                            <?= e(
                                $bairro['nome_bairro']
                            ) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>


            <!-- SENHA -->

            <div class="campo">

                <label for="senha">
                    Senha
                </label>

                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Mínimo de 8 caracteres"
                    minlength="8"
                    required
                >

            </div>


            <!-- CONFIRMAR SENHA -->

            <div class="campo">

                <label for="confirmar_senha">
                    Confirmar senha
                </label>

                <input
                    type="password"
                    id="confirmar_senha"
                    name="confirmar_senha"
                    placeholder="Digite a senha novamente"
                    minlength="8"
                    required
                >

            </div>


        </div>


        <div class="acoes">


            <button
                type="submit"
                class="botao principal"
            >
                Cadastrar funcionário
            </button>


            <a
                href="painel_funcionario.php"
                class="botao secundario"
            >
                Voltar ao painel
            </a>


        </div>


    </form>

</main>


<footer>

    © <?= date('Y') ?>
    CicloManos - Painel do Funcionário

</footer>


<script>

    /* =========================================
       MÁSCARA CPF
    ========================================= */

    const cpf = document.getElementById('cpf');

    cpf.addEventListener('input', function () {

        let valor = this.value
            .replace(/\D/g, '')
            .slice(0, 11);

        valor = valor.replace(
            /(\d{3})(\d)/,
            '$1.$2'
        );

        valor = valor.replace(
            /(\d{3})(\d)/,
            '$1.$2'
        );

        valor = valor.replace(
            /(\d{3})(\d{1,2})$/,
            '$1-$2'
        );

        this.value = valor;

    });


    /* =========================================
       MÁSCARA CEP
    ========================================= */

    const cep = document.getElementById('cep');

    cep.addEventListener('input', function () {

        let valor = this.value
            .replace(/\D/g, '')
            .slice(0, 8);

        if (valor.length > 5) {

            valor =
                valor.slice(0, 5)
                + '-'
                + valor.slice(5);
        }

        this.value = valor;

    });

</script>


</body>
</html>