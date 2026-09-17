<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

function e(?string $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
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

    $bairros = $pdo
        ->query(
            'SELECT id_bairro, nome_bairro
             FROM bairros
             ORDER BY nome_bairro'
        )
        ->fetchAll();

} catch (PDOException $erro) {

    die(
        '<h2>Erro ao conectar ao banco de dados</h2>
        <p>Verifique se o MySQL está ligado no XAMPP e se o banco
        <strong>ciclomanos</strong> existe.</p>
        <p>Erro: ' . e($erro->getMessage()) . '</p>'
    );
}


/* =========================================================
   TOKEN CSRF
   ========================================================= */

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


/* =========================================================
   VARIÁVEIS
   ========================================================= */

$loginMessage = '';
$registerMessage = '';
$showRegister = false;

$campos = [
    'name' => '',
    'registerEmail' => '',
    'registerCpf' => '',
    'registerRua' => '',
    'registerCep' => '',
    'registerBairro' => ''
];


/* =========================================================
   MENSAGEM APÓS CADASTRO
   ========================================================= */

if (!empty($_SESSION['flash_login'])) {

    $loginMessage = (string)$_SESSION['flash_login'];

    unset($_SESSION['flash_login']);
}


/* =========================================================
   PROCESSAMENTO DOS FORMULÁRIOS
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (
        !is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)
    ) {

        if (isset($_POST['register'])) {

            $registerMessage =
                'Sessão expirada. Atualize a página e tente novamente.';

            $showRegister = true;

        } else {

            $loginMessage =
                'Sessão expirada. Atualize a página e tente novamente.';
        }


    /* =====================================================
       LOGIN
       ===================================================== */

    } elseif (isset($_POST['login'])) {

        $email = strtolower(
            trim((string)($_POST['loginEmail'] ?? ''))
        );

        $senha = (string)($_POST['loginPassword'] ?? '');


        if ($email === '' || $senha === '') {

            $loginMessage =
                'Preencha o e-mail e a senha.';


        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $loginMessage =
                'Digite um e-mail válido.';


        } else {

            try {

                $q = $pdo->prepare(
                    'SELECT
                        u.id_usuario,
                        u.senha_hash,
                        d.nome
                     FROM usuarios u
                     INNER JOIN dados_pessoais d
                        ON d.id_dado = u.id_dado
                     WHERE d.email = :email
                     LIMIT 1'
                );

                $q->execute([
                    'email' => $email
                ]);

                $usuario = $q->fetch();


                if (
                    $usuario &&
                    password_verify(
                        $senha,
                        $usuario['senha_hash']
                    )
                ) {

                    session_regenerate_id(true);

                    $_SESSION['id_usuario'] =
                        (int)$usuario['id_usuario'];

                    $_SESSION['nome_usuario'] =
                        $usuario['nome'];

                    $_SESSION['email_usuario'] =
                        $email;


                    /*
                     * LOGIN REALIZADO
                     *
                     * Depois você pode trocar esse endereço
                     * pela página da área do cliente.
                     */

                    


                } else {

                    $loginMessage =
                        'E-mail ou senha incorretos.';
                }


            } catch (PDOException $erro) {

                $loginMessage =
                    'Erro ao realizar o login. Verifique o banco de dados.';
            }
        }


    /* =====================================================
       CADASTRO
       ===================================================== */

    } elseif (isset($_POST['register'])) {

        $showRegister = true;


        /* Pega os campos enviados */

        foreach ($campos as $campo => $valor) {

            $campos[$campo] = trim(
                (string)($_POST[$campo] ?? '')
            );
        }


        $nome = $campos['name'];

        $email = strtolower(
            $campos['registerEmail']
        );

        $cpf = preg_replace(
            '/\D/',
            '',
            $campos['registerCpf']
        ) ?? '';

        $rua = $campos['registerRua'];

        $cep = preg_replace(
            '/\D/',
            '',
            $campos['registerCep']
        ) ?? '';

        $bairroId = filter_var(
            $campos['registerBairro'],
            FILTER_VALIDATE_INT
        );

        $senha = (string)(
            $_POST['registerPassword'] ?? ''
        );

        $confirmacao = (string)(
            $_POST['confirmPassword'] ?? ''
        );


        /* Lista de bairros válidos */

        $bairrosValidos = array_map(
            static function (array $bairro): int {
                return (int)$bairro['id_bairro'];
            },
            $bairros
        );


        /* =================================================
           VALIDAÇÕES
           ================================================= */

        if (mb_strlen($nome) < 3) {

            $registerMessage =
                'Digite seu nome completo.';


        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $registerMessage =
                'Digite um e-mail válido.';


        } elseif (!cpfValido($cpf)) {

            $registerMessage =
                'Digite um CPF válido.';


        } elseif (mb_strlen($rua) < 3) {

            $registerMessage =
                'Digite uma rua válida.';


        } elseif (strlen($cep) !== 8) {

            $registerMessage =
                'Digite um CEP com 8 números.';


        } elseif (
            $bairroId === false ||
            !in_array(
                $bairroId,
                $bairrosValidos,
                true
            )
        ) {

            $registerMessage =
                'Selecione um bairro válido.';


        } elseif (strlen($senha) < 8) {

            $registerMessage =
                'A senha deve ter pelo menos 8 caracteres.';


        } elseif ($senha !== $confirmacao) {

            $registerMessage =
                'As senhas não coincidem.';


        } else {


            /* =============================================
               CADASTRO NO BANCO
               ============================================= */

            try {

                $pdo->beginTransaction();


                /* -----------------------------------------
                   Verifica e-mail ou CPF
                   ----------------------------------------- */

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


                /* -----------------------------------------
                   Cadastra endereço
                   ----------------------------------------- */

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

                $enderecoId =
                    (int)$pdo->lastInsertId();


                /* -----------------------------------------
                   Cadastra dados pessoais
                   ----------------------------------------- */

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

                $dadoId =
                    (int)$pdo->lastInsertId();


                /* -----------------------------------------
                   Cadastra usuário
                   ----------------------------------------- */

                $q = $pdo->prepare(
                    'INSERT INTO usuarios
                    (id_dado, senha_hash)
                    VALUES
                    (:dado, :senha)'
                );

                $q->execute([
                    'dado' => $dadoId,
                    'senha' => password_hash(
                        $senha,
                        PASSWORD_DEFAULT
                    )
                ]);


                /* -----------------------------------------
                   Cadastra cliente
                   ----------------------------------------- */

                $q = $pdo->prepare(
                    'INSERT INTO clientes
                    (id_dado)
                    VALUES
                    (:dado)'
                );

                $q->execute([
                    'dado' => $dadoId
                ]);


                /* -----------------------------------------
                   Finaliza cadastro
                   ----------------------------------------- */

                $pdo->commit();


                $_SESSION['flash_login'] =
                    'Cadastro concluído! Agora entre com seu e-mail e senha.';


                header('Location: login.php');
                exit;


            } catch (RuntimeException $erro) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $registerMessage =
                    $erro->getMessage();


            } catch (PDOException $erro) {

                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }

                $registerMessage =
                    'Não foi possível concluir o cadastro. '
                    . 'Verifique as tabelas do banco.';
            }
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

    <title>Login / Cadastro - CicloManos</title>

    <link
        rel="stylesheet"
        href="login.css"
    >

</head>


<body>


<!-- =====================================================
     TOPO
     ===================================================== -->

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


<!-- =====================================================
     HEADER
     ===================================================== -->

<div class="meio-header">

    <a href="ciclomanos.php">

        <img
            src="fotos/logo.jpg"
            class="logo"
            alt="CicloManos"
        >

    </a>


    <div class="usuario">

        <a href="login.php">
            👤 Conta
        </a>

        <a href="#">
            🛒 Carrinho
        </a>

    </div>

</div>


<!-- =====================================================
     MENU
     ===================================================== -->

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

        <a href="manutencao.html">
            Manutenção
        </a>

        <a href="ofertas.html">
            Ofertas
        </a>

    </nav>

</div>


<!-- =====================================================
     ÁREA DE LOGIN / CADASTRO
     ===================================================== -->

<main class="login-area">

    <div class="container-login">


        <!-- LADO ESQUERDO -->

        <div class="welcome">

            <h1>
                Bem-vindo!
            </h1>

            <div class="linha"></div>

            <p>
                Entre na sua conta ou crie um novo cadastro
                para aproveitar tudo o que a CicloManos tem
                para oferecer.
            </p>

        </div>


        <!-- LADO DOS FORMULÁRIOS -->

        <div class="forms">


            <!-- =================================================
                 FORMULÁRIO DE LOGIN
                 ================================================= -->

            <form
                class="form"
                id="loginForm"
                method="POST"
                action="login.php"
                style="<?php echo $showRegister ? 'display:none;' : 'display:block;'; ?>"
            >

                <h2>
                    Entrar
                </h2>

                <p class="form-subtitulo">
                    Acesse sua conta CicloManos
                </p>


                <!-- CSRF -->

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo e($_SESSION['csrf_token']); ?>"
                >


                <!-- E-MAIL -->

                <div class="input-group">

                    <label for="loginEmail">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="loginEmail"
                        name="loginEmail"
                        placeholder="seu@email.com"
                        required
                    >

                </div>


                <!-- SENHA -->

                <div class="input-group">

                    <label for="loginPassword">
                        Senha
                    </label>

                    <input
                        type="password"
                        id="loginPassword"
                        name="loginPassword"
                        placeholder="Digite sua senha"
                        required
                    >

                </div>


                <!-- BOTÃO -->

                <button
                    class="button"
                    type="submit"
                    name="login"
                >
                    Entrar
                </button>


                <!-- TROCAR PARA CADASTRO -->

                <div class="switch">

                    Ainda não possui uma conta?

                    <a
                        href="#"
                        onclick="showRegister(); return false;"
                    >
                        Criar conta
                    </a>

                </div>


                <!-- MENSAGEM -->

                <?php if (!empty($loginMessage)): ?>

                    <div
                        class="message"
                        id="loginMessage"
                    >

                        <?php echo e($loginMessage); ?>

                    </div>

                <?php endif; ?>


            </form>


            <!-- =================================================
                 FORMULÁRIO DE CADASTRO
                 ================================================= -->

            <form
                class="form"
                id="registerForm"
                method="POST"
                action="login.php"
                style="<?php echo $showRegister ? 'display:block;' : 'display:none;'; ?>"
            >

                <h2>
                    Criar conta
                </h2>

                <p class="form-subtitulo">
                    Cadastre-se na CicloManos
                </p>


                <!-- CSRF -->

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?php echo e($_SESSION['csrf_token']); ?>"
                >


                <!-- NOME -->

                <div class="input-group">

                    <label for="name">
                        Nome
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Seu nome completo"
                        value="<?php echo e($campos['name']); ?>"
                        required
                    >

                </div>


                <!-- E-MAIL -->

                <div class="input-group">

                    <label for="registerEmail">
                        E-mail
                    </label>

                    <input
                        type="email"
                        id="registerEmail"
                        name="registerEmail"
                        placeholder="seu@email.com"
                        value="<?php echo e($campos['registerEmail']); ?>"
                        required
                    >

                </div>


                <!-- CPF -->

                <div class="input-group">

                    <label for="registerCpf">
                        CPF
                    </label>

                    <input
                        type="text"
                        id="registerCpf"
                        name="registerCpf"
                        placeholder="000.000.000-00"
                        value="<?php echo e($campos['registerCpf']); ?>"
                        maxlength="14"
                        required
                    >

                </div>


                <!-- RUA -->

                <div class="input-group">

                    <label for="registerRua">
                        Rua
                    </label>

                    <input
                        type="text"
                        id="registerRua"
                        name="registerRua"
                        placeholder="Nome da sua rua"
                        value="<?php echo e($campos['registerRua']); ?>"
                        required
                    >

                </div>


                <!-- CEP -->

                <div class="input-group">

                    <label for="registerCep">
                        CEP
                    </label>

                    <input
                        type="text"
                        id="registerCep"
                        name="registerCep"
                        placeholder="00000-000"
                        value="<?php echo e($campos['registerCep']); ?>"
                        maxlength="9"
                        required
                    >

                </div>


                <!-- BAIRRO -->

                <div class="input-group">

                    <label for="registerBairro">
                        Bairro
                    </label>

                    <select
                        id="registerBairro"
                        name="registerBairro"
                        required
                    >

                        <option value="">
                            Selecione seu bairro
                        </option>


                        <?php foreach ($bairros as $bairro): ?>

                            <option
                                value="<?php echo (int)$bairro['id_bairro']; ?>"
                                <?php
                                echo (
                                    (string)$campos['registerBairro']
                                    ===
                                    (string)$bairro['id_bairro']
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >

                                <?php
                                echo e($bairro['nome_bairro']);
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- SENHA -->

                <div class="input-group">

                    <label for="registerPassword">
                        Senha
                    </label>

                    <input
                        type="password"
                        id="registerPassword"
                        name="registerPassword"
                        placeholder="Crie uma senha"
                        minlength="8"
                        required
                    >

                </div>


                <!-- CONFIRMAR SENHA -->

                <div class="input-group">

                    <label for="confirmPassword">
                        Confirmar senha
                    </label>

                    <input
                        type="password"
                        id="confirmPassword"
                        name="confirmPassword"
                        placeholder="Digite a senha novamente"
                        minlength="8"
                        required
                    >

                </div>


                <!-- BOTÃO CADASTRAR -->

                <button
                    class="button"
                    type="submit"
                    name="register"
                >
                    Cadastrar
                </button>


                <!-- VOLTAR PARA LOGIN -->

                <div class="switch">

                    Já possui uma conta?

                    <a
                        href="#"
                        onclick="showLogin(); return false;"
                    >
                        Fazer login
                    </a>

                </div>


                <!-- MENSAGEM -->

                <?php if (!empty($registerMessage)): ?>

                    <div
                        class="message"
                        id="registerMessage"
                    >

                        <?php echo e($registerMessage); ?>

                    </div>

                <?php endif; ?>


            </form>

        </div>

    </div>

</main>


<!-- =====================================================
     RODAPÉ
     ===================================================== -->

<footer>

    <p>
        © 2026 CicloManos - Todos os direitos reservados.
    </p>

</footer>


<!-- =====================================================
     JAVASCRIPT
     ===================================================== -->

<script>

function showRegister() {

    document.getElementById("loginForm").style.display = "none";

    document.getElementById("registerForm").style.display = "block";
}


function showLogin() {

    document.getElementById("registerForm").style.display = "none";

    document.getElementById("loginForm").style.display = "block";
}

</script>


</body>

</html>