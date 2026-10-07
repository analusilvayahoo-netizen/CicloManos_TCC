<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'funcionario') {
    header('Location: login.php');
    exit;
}

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

    $bairros = $pdo->query(
        'SELECT id_bairro, nome_bairro
         FROM bairros
         ORDER BY nome_bairro'
    )->fetchAll();
} catch (PDOException $erro) {
    die(
        '<h2>Erro ao conectar ao banco de dados</h2>' .
        '<p>Verifique se o MySQL está ligado e se o banco <strong>ciclomanos</strong> existe.</p>' .
        '<p>Erro: ' . e($erro->getMessage()) . '</p>'
    );
}

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

    $bairrosValidos = array_map(
        static fn(array $bairro): int => (int)$bairro['id_bairro'],
        $bairros
    );

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
    } elseif (
        $bairroId === false ||
        !in_array($bairroId, $bairrosValidos, true)
    ) {
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

            $q = $pdo->prepare(
                'SELECT id_dado
                 FROM dados_pessoais
                 WHERE email = :email OR cpf = :cpf
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

            $q = $pdo->prepare(
                'INSERT INTO enderecos (rua, cep, id_bairro)
                 VALUES (:rua, :cep, :bairro)'
            );
            $q->execute([
                'rua' => $rua,
                'cep' => $cep,
                'bairro' => $bairroId
            ]);

            $enderecoId = (int)$pdo->lastInsertId();

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

            $q = $pdo->prepare(
                'INSERT INTO usuarios
                    (id_dado, senha_hash, tipo)
                 VALUES
                    (:dado, :senha, 1)'
            );
            $q->execute([
                'dado' => $dadoId,
                'senha' => password_hash($senha, PASSWORD_DEFAULT)
            ]);

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

            $pdo->commit();

            $mensagem = 'Funcionário cadastrado com sucesso! Ele já pode entrar pelo login usando o e-mail @ciclomanos.com.';
            $sucesso = true;

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

            $mensagem = 'Não foi possível cadastrar o funcionário. Verifique as tabelas do banco de dados.';
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

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f5f5f5;
            color: #333;
        }

        header {
            background: #111;
            color: white;
            padding: 20px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 25px;
            font-weight: bold;
        }

        .funcionario-logado {
            text-align: right;
            font-size: 14px;
        }

        .funcionario-logado strong {
            display: block;
            font-size: 16px;
        }

        .funcionario-logado span {
            color: #ccc;
        }

        .container {
            width: 92%;
            max-width: 900px;
            margin: 40px auto;
        }

        .topo {
            margin-bottom: 25px;
        }

        .topo h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .topo p {
            color: #666;
        }

        .formulario {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
        }

        .campo {
            display: flex;
            flex-direction: column;
        }

        .campo-completo {
            grid-column: 1 / -1;
        }

        label {
            font-weight: bold;
            margin-bottom: 7px;
            font-size: 14px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: #111;
        }

        .ajuda {
            color: #777;
            font-size: 12px;
            margin-top: 5px;
        }

        .mensagem {
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        .sucesso {
            background: #e8f5e9;
            color: #246b2b;
            border: 1px solid #b7dfba;
        }

        .erro {
            background: #ffebee;
            color: #a52222;
            border: 1px solid #efb1b1;
        }

        .acoes {
            margin-top: 25px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .botao {
            display: inline-block;
            border: none;
            border-radius: 8px;
            padding: 13px 22px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s;
        }

        .principal {
            background: #111;
            color: white;
        }

        .principal:hover {
            background: #333;
        }

        .secundario {
            background: #e9e9e9;
            color: #333;
        }

        .secundario:hover {
            background: #d8d8d8;
        }

        footer {
            margin-top: 50px;
            background: #111;
            color: #aaa;
            text-align: center;
            padding: 20px;
            font-size: 13px;
        }

        @media (max-width: 650px) {
            header {
                padding: 20px;
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }

            .funcionario-logado {
                text-align: center;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .campo-completo {
                grid-column: auto;
            }

            .formulario {
                padding: 20px;
            }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">🚲 CicloManos</div>

    <div class="funcionario-logado">
        <strong><?= e($_SESSION['nome_usuario'] ?? 'Funcionário') ?></strong>
        <span><?= e($_SESSION['cargo_funcionario'] ?? 'Funcionário') ?></span>
    </div>
</header>

<main class="container">
    <div class="topo">
        <h1>Cadastrar Funcionário</h1>
        <p>Cadastre um novo funcionário para ter acesso ao painel administrativo.</p>
    </div>

    <?php if ($mensagem !== ''): ?>
        <div class="mensagem <?= $sucesso ? 'sucesso' : 'erro' ?>">
            <?= e($mensagem) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="cadastro_funcionario.php" class="formulario">
        <div class="grid">
            <div class="campo campo-completo">
                <label for="nome">Nome completo</label>
                <input
                    type="text"
                    id="nome"
                    name="nome"
                    value="<?= e($campos['nome']) ?>"
                    placeholder="Nome completo do funcionário"
                    required
                >
            </div>

            <div class="campo">
                <label for="email">E-mail profissional</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= e($campos['email']) ?>"
                    placeholder="nome@ciclomanos.com"
                    required
                >
                <span class="ajuda">Obrigatoriamente deve terminar em @ciclomanos.com.</span>
            </div>

            <div class="campo">
                <label for="cpf">CPF</label>
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

            <div class="campo">
                <label for="cargo">Cargo</label>
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

            <div class="campo">
                <label for="data_admissao">Data de admissão</label>
                <input
                    type="date"
                    id="data_admissao"
                    name="data_admissao"
                    value="<?= e($campos['data_admissao']) ?>"
                    required
                >
            </div>

            <div class="campo campo-completo">
                <label for="rua">Rua</label>
                <input
                    type="text"
                    id="rua"
                    name="rua"
                    value="<?= e($campos['rua']) ?>"
                    placeholder="Nome da rua"
                    required
                >
            </div>

            <div class="campo">
                <label for="cep">CEP</label>
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

            <div class="campo">
                <label for="bairro">Bairro</label>
                <select id="bairro" name="bairro" required>
                    <option value="">Selecione o bairro</option>

                    <?php foreach ($bairros as $bairro): ?>
                        <option
                            value="<?= (int)$bairro['id_bairro'] ?>"
                            <?= (string)$campos['bairro'] === (string)$bairro['id_bairro'] ? 'selected' : '' ?>
                        >
                            <?= e($bairro['nome_bairro']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="senha">Senha</label>
                <input
                    type="password"
                    id="senha"
                    name="senha"
                    placeholder="Mínimo de 8 caracteres"
                    minlength="8"
                    required
                >
            </div>

            <div class="campo">
                <label for="confirmar_senha">Confirmar senha</label>
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
            <button type="submit" class="botao principal">
                Cadastrar funcionário
            </button>

            <a href="painel_funcionario.php" class="botao secundario">
                Voltar ao painel
            </a>
        </div>
    </form>
</main>

<footer>
    © <?= date('Y') ?> CicloManos - Painel do Funcionário
</footer>

<script>
    const cpf = document.getElementById('cpf');
    cpf.addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, '').slice(0, 11);
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
        valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        this.value = valor;
    });

    const cep = document.getElementById('cep');
    cep.addEventListener('input', function () {
        let valor = this.value.replace(/\D/g, '').slice(0, 8);
        if (valor.length > 5) {
            valor = valor.slice(0, 5) + '-' + valor.slice(5);
        }
        this.value = valor;
    });
</script>

</body>
</html>
