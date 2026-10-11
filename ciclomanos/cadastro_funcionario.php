<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
session_start();

function e(?string $valor): string {
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function cpfValido(string $cpf): bool {
    $cpf = preg_replace('/\D/', '', $cpf) ?? '';
    if (strlen($cpf) !== 11) return false;
    if (preg_match('/^(\d)\1{10}$/', $cpf)) return false;
    for ($p = 9; $p < 11; $p++) {
        $soma = 0;
        for ($i = 0; $i < $p; $i++) {
            $soma += (int)$cpf[$i] * (($p + 1) - $i);
        }
        $digito = (($soma * 10) % 11) % 10;
        if ($digito !== (int)$cpf[$p]) return false;
    }
    return true;
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=ciclomanos;charset=utf8mb4', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    $bairros = $pdo->query('SELECT id_bairro, nome_bairro FROM bairros ORDER BY nome_bairro')->fetchAll();
    $quantidadeFuncionarios = (int) $pdo->query('SELECT COUNT(*) FROM funcionarios')->fetchColumn();
} catch (PDOException $erro) {
    die('<h2>Erro ao conectar ao banco de dados</h2><p>Erro: ' . e($erro->getMessage()) . '</p>');
}

$primeiroCadastro = ($quantidadeFuncionarios === 0);
$acessoLocal = in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true);
$funcionarioLogado = (($_SESSION['tipo_usuario'] ?? '') === 'funcionario');

if (!$funcionarioLogado && !($primeiroCadastro && $acessoLocal)) {
    header('Location: login_funcionario.php');
    exit;
}

$mensagem = '';
$sucesso = false;
$campos = ['nome' => '', 'email' => '', 'cpf' => '', 'rua' => '', 'cep' => '', 'bairro' => '', 'cargo' => '', 'data_admissao' => date('Y-m-d')];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($campos as $campo => $valor) {
        $campos[$campo] = trim((string)($_POST[$campo] ?? ''));
    }
    $nome = $campos['nome'];
    $email = strtolower($campos['email']);
    $cpf = preg_replace('/\D/', '', $campos['cpf']) ?? '';
    $rua = $campos['rua'];
    $cep = preg_replace('/\D/', '', $campos['cep']) ?? '';
    $bairroId = filter_var($campos['bairro'], FILTER_VALIDATE_INT);
    $cargo = $campos['cargo'];
    $dataAdmissao = $campos['data_admissao'];
    $senha = (string)($_POST['senha'] ?? '');
    $confirmarSenha = (string)($_POST['confirmar_senha'] ?? '');
    $bairrosValidos = array_map(static fn(array $bairro): int => (int)$bairro['id_bairro'], $bairros);

    if (mb_strlen($nome) < 3) {
        $mensagem = 'Digite o nome completo do funcionário.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $mensagem = 'Digite um e-mail válido.';
    } elseif (!str_ends_with($email, '@ciclomanos.com')) {
        $mensagem = 'O e-mail do funcionário deve terminar com @ciclomanos.com.';
    } elseif (!cpfValido($cpf)) {
        $mensagem = 'Digite um CPF válido.';
    } elseif (mb_strlen($rua) < 3) {
        $mensagem = 'Digite uma rua válida.';
    } elseif (strlen($cep) !== 8) {
        $mensagem = 'Digite um CEP com 8 números.';
    } elseif ($bairroId === false || !in_array($bairroId, $bairrosValidos, true)) {
        $mensagem = 'Selecione um bairro válido.';
    } elseif (mb_strlen($cargo) < 2) {
        $mensagem = 'Digite o cargo do funcionário.';
    } elseif ($dataAdmissao === '') {
        $mensagem = 'Informe a data de admissão.';
    } elseif (strlen($senha) < 8) {
        $mensagem = 'A senha deve ter pelo menos 8 caracteres.';
    } elseif ($senha !== $confirmarSenha) {
        $mensagem = 'As senhas não coincidem.';
    } else {
        try {
            $pdo->beginTransaction();
            $q = $pdo->prepare('SELECT id_dado FROM dados_pessoais WHERE email = :email OR cpf = :cpf LIMIT 1');
            $q->execute(['email' => $email, 'cpf' => $cpf]);
            if ($q->fetch()) {
                throw new RuntimeException('Já existe um cadastro com este e-mail ou CPF.');
            }

            $q = $pdo->prepare('INSERT INTO enderecos (rua, cep, id_bairro) VALUES (:rua, :cep, :bairro)');
            $q->execute(['rua' => $rua, 'cep' => $cep, 'bairro' => $bairroId]);
            $enderecoId = (int)$pdo->lastInsertId();

            $q = $pdo->prepare('INSERT INTO dados_pessoais (cpf, nome, email, id_endereco) VALUES (:cpf, :nome, :email, :endereco)');
            $q->execute(['cpf' => $cpf, 'nome' => $nome, 'email' => $email, 'endereco' => $enderecoId]);
            $dadoId = (int)$pdo->lastInsertId();

            $q = $pdo->prepare('INSERT INTO usuarios (id_dado, senha_hash, tipo) VALUES (:dado, :senha, :tipo)');
            $q->execute(['dado' => $dadoId, 'senha' => password_hash($senha, PASSWORD_DEFAULT), 'tipo' => 'funcionario']);

            $q = $pdo->prepare('INSERT INTO funcionarios (id_dado, cargo, data_admissao) VALUES (:dado, :cargo, :data_admissao)');
            $q->execute(['dado' => $dadoId, 'cargo' => $cargo, 'data_admissao' => $dataAdmissao]);

            $pdo->commit();
            $mensagem = 'Funcionário cadastrado com sucesso!';
            $sucesso = true;
            $campos = ['nome' => '', 'email' => '', 'cpf' => '', 'rua' => '', 'cep' => '', 'bairro' => '', 'cargo' => '', 'data_admissao' => date('Y-m-d')];
        } catch (RuntimeException $erro) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $mensagem = $erro->getMessage();
        } catch (PDOException $erro) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $mensagem = 'Não foi possível cadastrar o funcionário.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Funcionário - CicloManos</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { min-height: 100vh; background: #f7f7f7; color: #222; display: flex; flex-direction: column; font-family: Arial, sans-serif; }
        .container { width: 92%; max-width: 900px; margin: 45px auto 60px; flex: 1; }
        .topo { margin-bottom: 28px; }
        .topo h1 { font-size: 28px; font-weight: 700; margin-bottom: 6px; color: #111; }
        .formulario { background: #fff; border: 1px solid #e8e8e8; border-radius: 14px; padding: 32px; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05); }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .campo { display: flex; flex-direction: column; }
        .campo-completo { grid-column: 1 / -1; }
        label { font-weight: 600; font-size: 13px; color: #333; margin-bottom: 8px; }
        input, select { width: 100%; height: 46px; padding: 0 13px; border: 1px solid #d6d6d6; border-radius: 8px; font-size: 14px; outline: none; }
        input:focus, select:focus { border-color: #111; }
        .ajuda { margin-top: 6px; color: #888; font-size: 11px; }
        .mensagem { padding: 15px 17px; border-radius: 10px; margin-bottom: 20px; font-size: 14px; }
        .sucesso { background: #f0f7f1; color: #286331; border: 1px solid #c9dfcc; }
        .erro { background: #faf0f0; color: #a52222; border: 1px solid #e4c4c4; }
        .acoes { margin-top: 28px; display: flex; gap: 12px; }
        .botao { min-height: 44px; padding: 0 22px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; border: none; }
        .principal { background: #111; color: #fff; }
        .principal:hover { background: #333; color: #fff; }
        .secundario { background: #f1f1f1; color: #333; border: 1px solid #ddd; }
        .secundario:hover { background: #e5e5e5; color: #333; }
        footer { background: #111; color: #aaa; text-align: center; padding: 22px; font-size: 12px; margin-top: auto; width: 100%; }
        @media(max-width: 650px) { .grid { grid-template-columns: 1fr; } .campo-completo { grid-column: auto; } }
    </style>
</head>
<body>

<div class="topo-cinza">
    <a href="https://share.google/jYgrtVLyebEBGaqzt" target="_blank">📍 Localização</a>
    <a href="https://wa.me/551239163262?text=Olá!%20Vim%20pelo%20site%20da%20CicloManos" target="_blank">💬 Fale conosco</a>
    <a href="https://wa.me/551239163262" target="_blank">📱 WhatsApp: (12) 3916-3262</a>
    <span>📞 Telefone: (12) 3916-3262</span>
</div>

<header class="meio-header">
    <a href="Index.php">
        <img src="tcc/logo.jpg" class="logo" alt="Logo CicloManos">
    </a>
    <div class="usuario">
        <a href="painel_funcionario.php">👤 Olá, <?= e($_SESSION['nome_usuario'] ?? 'Funcionário') ?>!</a>
        <a href="logout.php">🚪 Sair</a>
    </div>
</header>

<div class="menu-bar">
    <div class="produtos-menu">
        <button type="button" class="botao-produtos" onclick="abrirProdutos()">☰ Produtos</button>
        <div id="caixa-produtos" class="caixa-produtos">
            <a href="acessorios.php">Acessórios</a>
            <a href="pecas.php">Peças</a>
            <a href="bicicletas.php">Bicicletas</a>
        </div>
    </div>
    <nav>
        <a href="manutencao.php">Manutenção</a>
        <a href="ofertas.php">Ofertas</a>
    </nav>
</div>

<main class="container">
    <div class="topo">
        <h1>Cadastrar Funcionário</h1>
        <p>Cadastre um novo membro para a equipe com acesso administrativo.</p>
    </div>
    <?php if ($mensagem !== ''): ?>
        <div class="mensagem <?= $sucesso ? 'sucesso' : 'erro' ?>"><?= e($mensagem) ?></div>
    <?php endif; ?>
    <form method="POST" action="cadastro_funcionario.php" class="formulario">
        <div class="grid">
            <div class="campo campo-completo">
                <label for="nome">Nome completo</label>
                <input type="text" id="nome" name="nome" value="<?= e($campos['nome']) ?>" required>
            </div>
            <div class="campo">
                <label for="email">E-mail profissional</label>
                <input type="email" id="email" name="email" value="<?= e($campos['email']) ?>" placeholder="nome@ciclomanos.com" required>
                <span class="ajuda">Termina obrigatoriamente com @ciclomanos.com</span>
            </div>
            <div class="campo">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="<?= e($campos['cpf']) ?>" maxlength="14" required>
            </div>
            <div class="campo">
                <label for="cargo">Cargo</label>
                <input type="text" id="cargo" name="cargo" value="<?= e($campos['cargo']) ?>" placeholder="Ex.: Mecânico" required>
            </div>
            <div class="campo">
                <label for="data_admissao">Data de admissão</label>
                <input type="date" id="data_admissao" name="data_admissao" value="<?= e($campos['data_admissao']) ?>" required>
            </div>
            <div class="campo campo-completo">
                <label for="rua">Rua</label>
                <input type="text" id="rua" name="rua" value="<?= e($campos['rua']) ?>" required>
            </div>
            <div class="campo">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" value="<?= e($campos['cep']) ?>" maxlength="9" required>
            </div>
            <div class="campo">
                <label for="bairro">Bairro</label>
                <select id="bairro" name="bairro" required>
                    <option value="">Selecione o bairro</option>
                    <?php foreach ($bairros as $bairro): ?>
                        <option value="<?= (int)$bairro['id_bairro'] ?>" <?= ((string)$campos['bairro'] === (string)$bairro['id_bairro']) ? 'selected' : '' ?>><?= e($bairro['nome_bairro']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" minlength="8" required>
            </div>
            <div class="campo">
                <label for="confirmar_senha">Confirmar senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" minlength="8" required>
            </div>
        </div>
        <div class="acoes">
            <button type="submit" class="botao principal">Cadastrar funcionário</button>
            <a href="painel_funcionario.php" class="botao secundario">Voltar ao painel</a>
        </div>
    </form>
</main>

<footer>© <?= date('Y') ?> CicloManos - Painel do Funcionário</footer>

<script>
function abrirProdutos() {
    document.getElementById("caixa-produtos").classList.toggle("aberto");
}
document.addEventListener("click", function(event) {
    const produtosMenu = document.querySelector(".produtos-menu");
    const caixa = document.getElementById("caixa-produtos");
    if (produtosMenu && caixa && !produtosMenu.contains(event.target)) {
        caixa.classList.remove("aberto");
    }
});
</script>
</body>
</html>