<?php
session_start();

$loginMessage = "";
$loginMessageColor = "";

$registerMessage = "";
$registerMessageColor = "";

$showRegister = false;


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["login"])) {

    $email = trim($_POST["loginEmail"] ?? "");
    $password = $_POST["loginPassword"] ?? "";

    if (empty($email) || empty($password)) {

        $loginMessage = "Preencha todos os campos!";
        $loginMessageColor = "#e60023";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $loginMessage = "Digite um e-mail válido!";
        $loginMessageColor = "#e60023";

    } else {

        // Aqui futuramente podemos consultar o banco de dados.
        $loginMessage = "Login enviado com sucesso!";
        $loginMessageColor = "#4A86B8";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {

    $showRegister = true;

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["registerEmail"] ?? "");
    $password = $_POST["registerPassword"] ?? "";
    $confirmPassword = $_POST["confirmPassword"] ?? "";

    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {

        $registerMessage = "Preencha todos os campos!";
        $registerMessageColor = "#e60023";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $registerMessage = "Digite um e-mail válido!";
        $registerMessageColor = "#e60023";

    } elseif ($password !== $confirmPassword) {

        $registerMessage = "As senhas não são iguais!";
        $registerMessageColor = "#e60023";

    } elseif (strlen($password) < 6) {

        $registerMessage = "A senha deve ter pelo menos 6 caracteres!";
        $registerMessageColor = "#e60023";

    } else {

        // Segurança:
        // quando conectarmos ao banco, a senha deverá ser salva assim:
        // $senhaHash = password_hash($password, PASSWORD_DEFAULT);

        $registerMessage = "Cadastro realizado com sucesso!";
        $registerMessageColor = "#4A86B8";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login / Cadastro - CicloManos</title>

    <link rel="stylesheet" href="login.css">

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



    <div class="menu">

        <a href="departamentos.html" class="departamentos">
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



    <main class="login-area">

        <div class="container-login">


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


       

            <div class="forms">


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


                    <button
                        class="button"
                        type="submit"
                        name="login"
                    >
                        Entrar
                    </button>


                    <div class="switch">

                        Ainda não possui uma conta?

                        <a href="#" onclick="showRegister(); return false;">
                            Criar conta
                        </a>

                    </div>


                    <?php if (!empty($loginMessage)): ?>

                        <div
                            class="message"
                            id="loginMessage"
                            style="color: <?php echo htmlspecialchars($loginMessageColor); ?>;"
                        >
                            <?php echo htmlspecialchars($loginMessage); ?>
                        </div>

                    <?php endif; ?>

                </form>


            

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


                    <div class="input-group">

                        <label for="name">
                            Nome
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Seu nome"
                            required
                        >

                    </div>


                    <div class="input-group">

                        <label for="registerEmail">
                            E-mail
                        </label>

                        <input
                            type="email"
                            id="registerEmail"
                            name="registerEmail"
                            placeholder="seu@email.com"
                            required
                        >

                    </div>


                    <div class="input-group">

                        <label for="registerPassword">
                            Senha
                        </label>

                        <input
                            type="password"
                            id="registerPassword"
                            name="registerPassword"
                            placeholder="Crie uma senha"
                            required
                        >

                    </div>


                    <div class="input-group">

                        <label for="confirmPassword">
                            Confirmar senha
                        </label>

                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="Digite a senha novamente"
                            required
                        >

                    </div>


                    <button
                        class="button"
                        type="submit"
                        name="register"
                    >
                        Cadastrar
                    </button>


                    <div class="switch">

                        Já possui uma conta?

                        <a href="#" onclick="showLogin(); return false;">
                            Fazer login
                        </a>

                    </div>


                    <?php if (!empty($registerMessage)): ?>

                        <div
                            class="message"
                            id="registerMessage"
                            style="color: <?php echo htmlspecialchars($registerMessageColor); ?>;"
                        >
                            <?php echo htmlspecialchars($registerMessage); ?>
                        </div>

                    <?php endif; ?>

                </form>

            </div>

        </div>

    </main>




    <footer>

        <p>
            © 2026 CicloManos - Todos os direitos reservados.
        </p>

    </footer>



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