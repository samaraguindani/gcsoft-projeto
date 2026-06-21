<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';
use App\Database;

if (isset($_SESSION['usuario'])) {
    header('Location: /');
    exit;
}

$erro = '';
$env      = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'local';
$envColor = match($env) { 'homolog' => '#f59e0b', 'production' => '#10b981', default => '#6366f1' };
$envLabel = match($env) { 'homolog' => '🔶 HOMOLOGAÇÃO', 'production' => '🟢 PRODUÇÃO', default => '🔵 LOCAL' };

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo  = Database::connect();
        $stmt = $pdo->prepare('SELECT * FROM usuario WHERE login = :login AND situacao = "ativo"');
        $stmt->execute([':login' => $_POST['login'] ?? '']);
        $user = $stmt->fetch();
        if ($user && password_verify($_POST['senha'] ?? '', $user['senha'])) {
            $_SESSION['usuario'] = ['id' => $user['id'], 'nome' => $user['nome']];
            header('Location: /');
            exit;
        }
        $erro = 'Login ou senha inválidos.';
    } catch (Exception $e) {
        $erro = 'Erro de conexão com o banco.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Receitas GCSoft</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f1f5f9; min-height:100vh; display:flex; flex-direction:column; }
        .env-bar { background:<?= $envColor ?>; color:white; text-align:center; padding:8px; font-weight:bold; font-size:14px; letter-spacing:1px; }
        .container { flex:1; display:flex; align-items:center; justify-content:center; padding:20px; }
        .card { background:white; border-radius:16px; padding:40px; box-shadow:0 4px 20px rgba(0,0,0,.1); width:100%; max-width:400px; }
        .logo { text-align:center; margin-bottom:28px; }
        .logo h1 { font-size:28px; color:#1e293b; }
        .logo p { color:#64748b; font-size:14px; margin-top:4px; }
        label { display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px; }
        input { width:100%; padding:11px 14px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; margin-bottom:16px; outline:none; }
        input:focus { border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,.1); }
        .btn { width:100%; padding:12px; background:#3b82f6; color:white; border:none; border-radius:8px; font-size:15px; font-weight:600; cursor:pointer; }
        .btn:hover { background:#2563eb; }
        .erro { background:#fee2e2; color:#991b1b; padding:10px 14px; border-radius:8px; font-size:13px; margin-bottom:16px; }
        .hint { text-align:center; font-size:12px; color:#94a3b8; margin-top:16px; }
    </style>
</head>
<body>
<div class="env-bar"><?= $envLabel ?> — Sistema de Receitas</div>
<div class="container">
    <div class="card">
        <div class="logo">
            <h1>🍰 Receitas</h1>
            <p>Sistema de Gerenciamento de Receitas</p>
        </div>
        <?php if ($erro): ?>
        <div class="erro">⚠️ <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label>Login</label>
            <input type="text" name="login" placeholder="Digite seu login" required autofocus>
            <label>Senha</label>
            <input type="password" name="senha" placeholder="Digite sua senha" required>
            <button type="submit" class="btn">Entrar</button>
        </form>
        <p class="hint">Login padrão: admin / admin123</p>
    </div>
</div>
</body>
</html>
