<?php
declare(strict_types=1);
session_start();

function e(?string $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

if (($_SESSION['tipo_usuario'] ?? '') === 'funcionario') {
    header('Location: painel_funcionario.php');
    exit;
}

$mensagem = '';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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
} catch (PDOException $erro) {
    http_response_code(500);
    die('Não foi possível conectar ao banco de dados. Verifique o MySQL e a configuração do CicloManos.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $senha = (string)($_POST['senha'] ?? '');

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $mensagem = 'Sessão expirada. Atualize a página e tente novamente.';
    } elseif ($email === '' || $senha === '') {
        $mensagem = 'Preencha o e-mail e a senha.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Digite um e-mail válido.';
    } else {
        try {
            $q = $pdo->prepare(
                'SELECT u.id_usuario, u.id_dado, u.senha_hash, u.tipo, d.nome, d.email
                 FROM usuarios u
                 INNER JOIN dados_pessoais d ON d.id_dado = u.id_dado
                 WHERE d.email = :email
                 LIMIT 1'
            );
            $q->execute(['email' => $email]);
            $usuario = $q->fetch();

            if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
                $mensagem = 'E-mail ou senha incorretos.';
            } elseif (($usuario['tipo'] ?? '') !== 'funcionario') {
                $mensagem = 'Esta conta não está cadastrada com o tipo funcionário. Verifique o cadastro no banco de dados.';
            } else {
                $q = $pdo->prepare(
                    'SELECT id_funcionario, cargo
                     FROM funcionarios
                     WHERE id_dado = :id_dado
                     LIMIT 1'
                );
                $q->execute(['id_dado' => (int)$usuario['id_dado']]);
                $funcionario = $q->fetch();

                if (!$funcionario) {
                    $mensagem = 'Esta conta não está cadastrada como funcionário.';
                } else {
                    session_regenerate_id(true);
                    unset($_SESSION['id_cliente']);
                    $_SESSION['id_usuario'] = (int)$usuario['id_usuario'];
                    $_SESSION['id_funcionario'] = (int)$funcionario['id_funcionario'];
                    $_SESSION['nome_usuario'] = $usuario['nome'];
                    $_SESSION['email_usuario'] = $email;
                    $_SESSION['tipo_usuario'] = 'funcionario';
                    $_SESSION['cargo_funcionario'] = $funcionario['cargo'];

                    header('Location: painel_funcionario.php');
                    exit;
                }
            }
        } catch (PDOException $erro) {
            $mensagem = 'Erro ao realizar o login. Verifique o banco de dados.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Funcionário - CicloManos</title>
    <link rel="stylesheet" href="login.css?v=2">
    <style>
        html, body { min-height: 100%; }
        body { min-height: 100vh; margin: 0; display: flex; flex-direction: column; }
        .login-area { flex: 1; width: 100%; display: flex; align-items: center; justify-content: center; box-sizing: border-box; }
        .container-login { margin: 30px auto; }
        footer { margin-top: auto; width: 100%; }
        .voltar-cliente { display: block; margin-top: 18px; text-align: center; }
    </style>
</head>
<body>
<main class="login-area">
    <div class="container-login">
        <section class="welcome">
            <h1>Área interna</h1>
            <div class="linha"></div>
            <p>Entre com sua conta profissional para acessar o painel de gerenciamento da CicloManos.</p>
        </section>
        <div class="forms">
            <form class="form" method="POST" action="login_funcionario.php">
                <h2>Login do funcionário</h2>
                <p class="form-subtitulo">Acesso exclusivo para a equipe</p>
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                <div class="input-group">
                    <label for="email">E-mail profissional</label>
                    <input type="email" id="email" name="email" placeholder="seu e-mail profissional" autocomplete="username" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
                </div>
                <button class="button" type="submit">Entrar no painel</button>
                <?php if ($mensagem !== ''): ?>
                    <div class="message" role="alert"><?= e($mensagem) ?></div>
                <?php endif; ?>
                <a class="voltar-cliente" href="login.php">Voltar ao login do cliente</a>
            </form>
        </div>
    </div>
</main>
<footer><p>© <?= date('Y') ?> CicloManos - Área do Funcionário</p></footer>
</body>
</html>
