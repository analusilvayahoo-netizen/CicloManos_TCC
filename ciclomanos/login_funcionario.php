<?php
declare(strict_types=1);
session_start();

function e(?string $valor): string {
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
    $pdo = new PDO('mysql:host=localhost;dbname=ciclomanos;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $erro) {
    http_response_code(500);
    die('Não foi possível conectar ao banco de dados.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $senha = (string)($_POST['senha'] ?? '');

    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        $mensagem = 'Sessão expirada. Tente novamente.';
    } elseif ($email === '' || $senha === '') {
        $mensagem = 'Preencha o e-mail e a senha.';
    } else {
        try {
            $q = $pdo->prepare('SELECT u.id_usuario, u.id_dado, u.senha_hash, d.nome, d.email FROM usuarios u INNER JOIN dados_pessoais d ON d.id_dado = u.id_dado WHERE d.email = :email LIMIT 1');
            $q->execute(['email' => $email]);
            $usuario = $q->fetch();

            if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
                $mensagem = 'E-mail ou senha incorretos.';
            } else {
                $q = $pdo->prepare('SELECT id_funcionario, cargo FROM funcionarios WHERE id_dado = :id_dado LIMIT 1');
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
            $mensagem = 'Erro ao realizar o login.';
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
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body { min-height: 100vh; margin: 0; }
        body { background: #f7f7f7; display: flex; flex-direction: column; font-family: Arial, sans-serif; }
        .login-area { flex: 1; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .container-login { background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); width: 100%; max-width: 420px; border: 1px solid #e8e8e8; }
        h2 { margin-bottom: 8px; color: #111; font-weight: bold; }
        p { color: #666; font-size: 14px; margin-bottom: 24px; }
        .input-group { margin-bottom: 16px; }
        label { display: block; font-size: 13px; font-weight: bold; margin-bottom: 6px; color: #333; }
        input { width: 100%; height: 44px; padding: 0 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; outline: none; }
        input:focus { border-color: #4A86B8; box-shadow: 0 0 0 2px rgba(74, 134, 184, 0.15); }
        .button { width: 100%; height: 44px; background: #4A86B8; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; transition: 0.2s; }
        .button:hover { background: #345f85; }
        .message { background: #fde8e8; color: #9b1c1c; padding: 10px; border-radius: 6px; margin-top: 14px; font-size: 13px; text-align: center; }
        .voltar-cliente { display: block; margin-top: 16px; text-align: center; color: #4A86B8; font-size: 13px; text-decoration: none; font-weight: bold; }
        .voltar-cliente:hover { text-decoration: underline; }
        footer { background: #111; color: #aaa; text-align: center; padding: 20px; font-size: 12px; margin-top: auto; width: 100%; }
    </style>
</head>
<body>

<main class="login-area">
    <div class="container-login">
        <h2>Login do funcionário</h2>
        <p>Acesso exclusivo para a equipe CicloManos</p>
        <form method="POST" action="login_funcionario.php">
            <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
            <div class="input-group">
                <label for="email">E-mail profissional</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button class="button" type="submit">Entrar no painel</button>
            <?php if ($mensagem !== ''): ?>
                <div class="message"><?= e($mensagem) ?></div>
            <?php endif; ?>
            <a class="voltar-cliente" href="login.php">Voltar ao login do cliente</a>
        </form>
    </div>
</main>

<footer>© <?= date('Y') ?> CicloManos - Área do Funcionário</footer>

</body>
</html>