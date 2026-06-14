<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Task;
use App\TaskRepository;

// Conexão com banco via variáveis de ambiente
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '3306';
$name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'gcsoft';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'gcsoft';
$pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? 'gcsoft123';
$env  = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'local';

$pdo  = null;
$repo = null;
$dbError = null;

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $repo = new TaskRepository($pdo);
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

// Ações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $repo) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add' && !empty($_POST['title'])) {
        $task = new Task(0, trim($_POST['title']), trim($_POST['description'] ?? ''));
        $repo->save($task);
    } elseif ($action === 'complete' && isset($_POST['id'])) {
        $task = $repo->findById((int)$_POST['id']);
        if ($task) { $task->complete(); $repo->update($task); }
    } elseif ($action === 'cancel' && isset($_POST['id'])) {
        $task = $repo->findById((int)$_POST['id']);
        if ($task) { $task->cancel(); $repo->update($task); }
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $repo->delete((int)$_POST['id']);
    }
    header('Location: /');
    exit;
}

$tasks    = $repo ? $repo->findAll() : [];
$envColor = match($env) { 'homolog' => '#f59e0b', 'production' => '#10b981', default => '#6366f1' };
$envLabel = match($env) { 'homolog' => '🔶 HOMOLOGAÇÃO', 'production' => '🟢 PRODUÇÃO', default => '🔵 LOCAL' };
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GCSoft - Gerenciador de Tarefas</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; min-height: 100vh; }
        .env-bar { background: <?= $envColor ?>; color: white; text-align: center;
                   padding: 8px; font-weight: bold; font-size: 14px; letter-spacing: 1px; }
        .container { max-width: 800px; margin: 30px auto; padding: 0 20px; }
        h1 { color: #1e293b; margin-bottom: 4px; }
        .subtitle { color: #64748b; font-size: 14px; margin-bottom: 24px; }
        .card { background: white; border-radius: 12px; padding: 24px;
                box-shadow: 0 1px 3px rgba(0,0,0,.1); margin-bottom: 20px; }
        .card h2 { font-size: 16px; color: #374151; margin-bottom: 16px; }
        input[type=text], textarea {
            width: 100%; padding: 10px 14px; border: 1px solid #d1d5db;
            border-radius: 8px; font-size: 14px; margin-bottom: 10px; }
        textarea { resize: vertical; height: 60px; }
        .btn { padding: 9px 18px; border: none; border-radius: 8px;
               font-size: 13px; font-weight: 600; cursor: pointer; }
        .btn-primary { background: #3b82f6; color: white; }
        .btn-success { background: #10b981; color: white; }
        .btn-danger  { background: #ef4444; color: white; }
        .btn-warning { background: #f59e0b; color: white; }
        .task-item { display: flex; align-items: center; gap: 10px;
                     padding: 12px; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 8px; }
        .task-title { flex: 1; font-size: 14px; color: #1e293b; font-weight: 500; }
        .task-desc  { font-size: 12px; color: #6b7280; }
        .badge { padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-pending   { background: #fef3c7; color: #92400e; }
        .badge-completed { background: #d1fae5; color: #065f46; }
        .badge-cancelled { background: #fee2e2; color: #991b1b; }
        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 20px; }
        .stat { background: white; border-radius: 10px; padding: 16px; text-align: center;
                box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .stat-value { font-size: 28px; font-weight: 700; color: #1e293b; }
        .stat-label { font-size: 12px; color: #6b7280; margin-top: 4px; }
        .error { background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        form { display: inline; }
    </style>
</head>
<body>
<div class="env-bar"><?= $envLabel ?> — GCSoft Gerenciador de Tarefas</div>
<div class="container">
    <h1>📋 Gerenciador de Tarefas</h1>
    <p class="subtitle">Disciplina: Gerência de Configuração de Software | 2026/A</p>

    <?php if ($dbError): ?>
    <div class="error">⚠️ Erro de conexão com o banco: <?= htmlspecialchars($dbError) ?></div>
    <?php endif; ?>

    <?php if ($repo): ?>
    <div class="stats">
        <div class="stat">
            <div class="stat-value"><?= count($tasks) ?></div>
            <div class="stat-label">Total de Tarefas</div>
        </div>
        <div class="stat">
            <div class="stat-value" style="color:#10b981"><?= $repo->countByStatus('completed') ?></div>
            <div class="stat-label">Concluídas</div>
        </div>
        <div class="stat">
            <div class="stat-value" style="color:#f59e0b"><?= $repo->countByStatus('pending') ?></div>
            <div class="stat-label">Pendentes</div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <h2>➕ Nova Tarefa</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add">
            <input type="text" name="title" placeholder="Título da tarefa" required>
            <textarea name="description" placeholder="Descrição (opcional)"></textarea>
            <button type="submit" class="btn btn-primary">Adicionar Tarefa</button>
        </form>
    </div>

    <div class="card">
        <h2>📌 Tarefas</h2>
        <?php if (empty($tasks)): ?>
            <p style="color:#9ca3af; text-align:center; padding:20px">Nenhuma tarefa cadastrada.</p>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
            <div class="task-item">
                <div style="flex:1">
                    <div class="task-title">#<?= $task->getId() ?> — <?= htmlspecialchars($task->getTitle()) ?></div>
                    <?php if ($task->getDescription()): ?>
                    <div class="task-desc"><?= htmlspecialchars($task->getDescription()) ?></div>
                    <?php endif; ?>
                </div>
                <span class="badge badge-<?= $task->getStatus() ?>">
                    <?= match($task->getStatus()) { 'pending'=>'Pendente','completed'=>'Concluída','cancelled'=>'Cancelada' } ?>
                </span>
                <?php if ($task->isPending()): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="complete">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button class="btn btn-success" title="Concluir">✔</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="action" value="cancel">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button class="btn btn-warning" title="Cancelar">✖</button>
                </form>
                <?php endif; ?>
                <form method="POST" onsubmit="return confirm('Excluir esta tarefa?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $task->getId() ?>">
                    <button class="btn btn-danger" title="Excluir">🗑</button>
                </form>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
