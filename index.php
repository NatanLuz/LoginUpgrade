<?php
declare(strict_types=1);

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();

const MAX_TENTATIVAS = 5;
const BLOQUEIO_SEGUNDOS = 180;

$credenciais = [
    'email' => 'admin@exemplo.com',
    // Senha de teste: 123456
    // Gerada com password_hash('123456', PASSWORD_DEFAULT)
    // ATENÇÃO: O hash pode variar dependendo da versão do PHP, mas o password_verify() irá funcionar corretamente.
    // Exemplo de hash para a senha "123456"

    'senha_hash' => '$2y$10$L3EF0M8fU6fX8jJ3S1zx7.el7rX8m2Ihcb9vlyjo8fKHwrwSITd0i',
    'nome' => 'Administrador'
];

$_SESSION['tentativas'] = $_SESSION['tentativas'] ?? 0;
$_SESSION['bloqueado_ate'] = $_SESSION['bloqueado_ate'] ?? 0;
$_SESSION['csrf_token'] = $_SESSION['csrf_token'] ?? bin2hex(random_bytes(32));

$mensagemErro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], (string) $csrfToken)) {
        $mensagemErro = 'Requisicao invalida. Atualize a pagina e tente novamente.';
    } elseif ($acao === 'logout') {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        header('Location: index.php');
        exit;
    } elseif ($acao === 'login') {
        $agora = time();
        if ($agora < (int) $_SESSION['bloqueado_ate']) {
            $restante = (int) $_SESSION['bloqueado_ate'] - $agora;
            $mensagemErro = 'Muitas tentativas. Tente novamente em ' . $restante . ' segundos.';
        } else {
            $email = trim($_POST['email'] ?? '');
            $senha = trim($_POST['senha'] ?? '');

            $emailValido = filter_var($email, FILTER_VALIDATE_EMAIL);
            $credenciaisValidas = $emailValido
                && hash_equals($credenciais['email'], $email)
                && password_verify($senha, $credenciais['senha_hash']);

            if ($credenciaisValidas) {
                session_regenerate_id(true);
                $_SESSION['usuario_logado'] = true;
                $_SESSION['nome_usuario'] = $credenciais['nome'];
                $_SESSION['tentativas'] = 0;
                $_SESSION['bloqueado_ate'] = 0;
            } else {
                $_SESSION['tentativas']++;
                if ((int) $_SESSION['tentativas'] >= MAX_TENTATIVAS) {
                    $_SESSION['bloqueado_ate'] = time() + BLOQUEIO_SEGUNDOS;
                    $_SESSION['tentativas'] = 0;
                    $mensagemErro = 'Muitas tentativas. Aguarde 3 minutos e tente novamente.';
                } else {
                    $mensagemErro = 'Email ou senha incorretos. Tente novamente.';
                }
            }
        }
    }
}

$usuarioLogado = isset($_SESSION['usuario_logado']) && $_SESSION['usuario_logado'] === true;
$nomeUsuario = $_SESSION['nome_usuario'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Painel de Login</title>
</head>

<body>
    <main class="layout-principal">
        <section class="cartao-login">
            <div class="cabecalho-cartao">
                <p class="selo">Acesso Seguro</p>
                <h1>Entrar no Sistema</h1>
                <p class="subtitulo">Use suas credenciais para continuar.</p>
            </div>

            <?php if ($usuarioLogado): ?>
            <div class="feedback-sucesso" role="status" aria-live="polite">
                <h2>Login realizado com sucesso</h2>
                <p>Bem-vindo, <?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, 'UTF-8'); ?>.</p>
                <form method="post" class="form-logout">
                    <input type="hidden" name="csrf_token"
                        value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="acao" value="logout">
                    <button class="botao-secundario" type="submit">Sair</button>
                </form>
            </div>
            <?php else: ?>
            <?php if ($mensagemErro !== ''): ?>
            <div class="feedback-erro" role="alert">
                <?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php endif; ?>

            <form method="post" class="formulario-login" novalidate>
                <input type="hidden" name="csrf_token"
                    value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="acao" value="login">

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="seuemail@exemplo.com" required
                    autocomplete="username">

                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required
                    autocomplete="current-password">

                <button type="submit" class="botao-primario">Entrar</button>
            </form>

            <p class="dica-acesso">Teste rapido: admin@exemplo.com / 123456</p>
            <?php endif; ?>
        </section>
    </main>
</body>

</html>
