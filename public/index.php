<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: /login.php');
    exit;
}
require_once __DIR__ . '/../vendor/autoload.php';
use App\Database;
use App\Receita;
use App\ReceitaRepository;

$env      = $_ENV['APP_ENV'] ?? getenv('APP_ENV') ?? 'local';
$envColor = match($env) { 'homolog' => '#f59e0b', 'production' => '#10b981', default => '#6366f1' };
$envLabel = match($env) { 'homolog' => '🔶 HOMOLOGAÇÃO', 'production' => '🟢 PRODUÇÃO', default => '🔵 LOCAL' };

$pdo  = Database::connect();
$repo = new ReceitaRepository($pdo);

// CRUD
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $r = new Receita(0, $_POST['nome'], $_POST['descricao'], $_POST['data_registro'], (float)$_POST['custo'], $_POST['tipo_receita']);
        $repo->save($r);
    } elseif ($action === 'delete' && isset($_POST['id'])) {
        $repo->delete((int)$_POST['id']);
    } elseif ($action === 'edit' && isset($_POST['id'])) {
        $r = $repo->findById((int)$_POST['id']);
        if ($r) {
            $r->setNome($_POST['nome']);
            $r->setTipoReceita($_POST['tipo_receita']);
            $r->setCusto((float)$_POST['custo']);
            $repo->update($r);
        }
    } elseif ($action === 'logout') {
        session_destroy();
        header('Location: /login.php');
        exit;
    }
    header('Location: /');
    exit;
}

$filtro   = $_GET['tipo'] ?? '';
$receitas = $filtro ? $repo->findByTipo($filtro) : $repo->findAll();
$editId   = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editRec  = $editId ? $repo->findById($editId) : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas - GCSoft</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Segoe UI',sans-serif; background:#f1f5f9; min-height:100vh; }
        .env-bar { background:<?= $envColor ?>; color:white; text-align:center; padding:8px; font-weight:bold; font-size:14px; letter-spacing:1px; }
        .header { background:white; padding:16px 30px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 1px 3px rgba(0,0,0,.08); }
        .header h1 { font-size:20px; color:#1e293b; }
        .header-right { display:flex; align-items:center; gap:12px; font-size:13px; color:#64748b; }
        .container { max-width:1000px; margin:30px auto; padding:0 20px; }
        .stats { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:24px; }
        .stat { background:white; border-radius:10px; padding:16px; text-align:center; box-shadow:0 1px 3px rgba(0,0,0,.08); }
        .stat-value { font-size:28px; font-weight:700; color:#1e293b; }
        .stat-label { font-size:12px; color:#6b7280; margin-top:4px; }
        .card { background:white; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,.1); margin-bottom:20px; }
        .card h2 { font-size:16px; color:#374151; margin-bottom:16px; }
        .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
        .form-grid .full { grid-column:1/-1; }
        label { display:block; font-size:12px; font-weight:600; color:#374151; margin-bottom:4px; }
        input, select, textarea { width:100%; padding:9px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:13px; }
        textarea { resize:vertical; height:60px; }
        .btn { padding:9px 18px; border:none; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; }
        .btn-primary { background:#3b82f6; color:white; }
        .btn-danger  { background:#ef4444; color:white; }
        .btn-warning { background:#f59e0b; color:white; }
        .btn-sm { padding:5px 12px; font-size:12px; }
        .btn-logout { background:transparent; border:1px solid #d1d5db; color:#64748b; padding:6px 14px; border-radius:8px; font-size:13px; cursor:pointer; }
        .filters { display:flex; gap:8px; margin-bottom:16px; }
        .filter-btn { padding:6px 16px; border-radius:20px; border:1px solid #d1d5db; background:white; font-size:13px; cursor:pointer; text-decoration:none; color:#374151; }
        .filter-btn.active { background:#3b82f6; color:white; border-color:#3b82f6; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:10px 12px; font-size:12px; color:#6b7280; border-bottom:2px solid #f1f5f9; }
        td { padding:10px 12px; font-size:13px; color:#1e293b; border-bottom:1px solid #f8fafc; vertical-align:middle; }
        tr:hover td { background:#f8fafc; }
        .badge { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }
        .badge-doce    { background:#fce7f3; color:#9d174d; }
        .badge-salgada { background:#fef3c7; color:#92400e; }
        .actions { display:flex; gap:6px; }
        .empty { text-align:center; padding:40px; color:#94a3b8; }
    </style>
</head>
<body>
<div class="env-bar"><?= $envLabel ?> — Sistema de Receitas GCSoft</div>
<div class="header">
    <h1>🍰 Gerenciamento de Receitas TOP</h1>
    <div class="header-right">
        Olá, <strong><?= htmlspecialchars($_SESSION['usuario']['nome']) ?></strong>
        <form method="POST"><input type="hidden" name="action" value="logout">
        <button class="btn-logout">Sair</button></form>
    </div>
</div>

<div class="container">
    <div class="stats">
        <div class="stat">
            <div class="stat-value"><?= $repo->count() ?></div>
            <div class="stat-label">Total de Receitas</div>
        </div>
        <div class="stat">
            <div class="stat-value" style="color:#9d174d"><?= $repo->countByTipo('doce') ?></div>
            <div class="stat-label">🍬 Doces</div>
        </div>
        <div class="stat">
            <div class="stat-value" style="color:#92400e"><?= $repo->countByTipo('salgada') ?></div>
            <div class="stat-label">🥐 Salgadas</div>
        </div>
    </div>

    <!-- Formulário adicionar / editar -->
    <div class="card">
        <h2><?= $editRec ? '✏️ Editar Receita' : '➕ Nova Receita' ?></h2>
        <form method="POST">
            <input type="hidden" name="action" value="<?= $editRec ? 'edit' : 'add' ?>">
            <?php if ($editRec): ?><input type="hidden" name="id" value="<?= $editRec->getId() ?>"><?php endif; ?>
            <div class="form-grid">
                <div>
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?= htmlspecialchars($editRec?->getNome() ?? '') ?>" required>
                </div>
                <div>
                    <label>Tipo</label>
                    <select name="tipo_receita">
                        <option value="doce"    <?= ($editRec?->getTipoReceita() ?? '') === 'doce'    ? 'selected' : '' ?>>🍬 Doce</option>
                        <option value="salgada" <?= ($editRec?->getTipoReceita() ?? '') === 'salgada' ? 'selected' : '' ?>>🥐 Salgada</option>
                    </select>
                </div>
                <div>
                    <label>Data de Registro</label>
                    <input type="date" name="data_registro" value="<?= $editRec?->getDataRegistro() ?? date('Y-m-d') ?>" required>
                </div>
                <div>
                    <label>Custo (R$)</label>
                    <input type="number" name="custo" step="0.01" min="0" value="<?= $editRec?->getCusto() ?? '' ?>" required>
                </div>
                <div class="full">
                    <label>Descrição</label>
                    <textarea name="descricao" required><?= htmlspecialchars($editRec?->getDescricao() ?? '') ?></textarea>
                </div>
                <div class="full">
                    <button type="submit" class="btn btn-primary"><?= $editRec ? 'Salvar Alterações' : 'Adicionar Receita' ?></button>
                    <?php if ($editRec): ?>
                    <a href="/" style="margin-left:8px;font-size:13px;color:#64748b">Cancelar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <!-- Listagem -->
    <div class="card">
        <h2>📋 Receitas Cadastradas</h2>
        <div class="filters">
            <a href="/"        class="filter-btn <?= !$filtro ? 'active' : '' ?>">Todas</a>
            <a href="/?tipo=doce"    class="filter-btn <?= $filtro==='doce' ? 'active' : '' ?>">🍬 Doces</a>
            <a href="/?tipo=salgada" class="filter-btn <?= $filtro==='salgada' ? 'active' : '' ?>">🥐 Salgadas</a>
        </div>
        <?php if (empty($receitas)): ?>
        <div class="empty">Nenhuma receita encontrada.</div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Custo</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($receitas as $r): ?>
                <tr>
                    <td><?= $r->getId() ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r->getNome()) ?></strong><br>
                        <span style="color:#6b7280;font-size:12px"><?= htmlspecialchars(mb_substr($r->getDescricao(),0,60)) ?>...</span>
                    </td>
                    <td><span class="badge badge-<?= $r->getTipoReceita() ?>"><?= $r->getTipoReceita() === 'doce' ? '🍬 Doce' : '🥐 Salgada' ?></span></td>
                    <td>R$ <?= number_format($r->getCusto(), 2, ',', '.') ?></td>
                    <td><?= date('d/m/Y', strtotime($r->getDataRegistro())) ?></td>
                    <td>
                        <div class="actions">
                            <a href="/?edit=<?= $r->getId() ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                            <form method="POST" onsubmit="return confirm('Excluir esta receita?')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $r->getId() ?>">
                                <button class="btn btn-danger btn-sm">🗑 Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
