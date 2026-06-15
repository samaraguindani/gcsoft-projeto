<?php
/**
 * Script de Migrations simples (substitui Flyway)
 * Uso: php db/migrate.php homolog
 *      php db/migrate.php prod
 */

$env = $argv[1] ?? 'homolog';

$config = [
    'homolog' => [
        'host' => '127.0.0.1',
        'port' => '3307',
        'name' => 'gcsoft_homolog',
        'user' => 'gcsoft',
        'pass' => 'gcsoft123',
    ],
    'prod' => [
        'host' => '127.0.0.1',
        'port' => '3307',
        'name' => 'gcsoft_prod',
        'user' => 'gcsoft',
        'pass' => 'gcsoft123',
    ],
];

if (!isset($config[$env])) {
    echo "❌ Ambiente inválido. Use: php db/migrate.php homolog|prod\n";
    exit(1);
}

$c = $config[$env];

echo "╔══════════════════════════════════════════╗\n";
echo "║  MIGRATIONS - " . strtoupper($env) . str_repeat(' ', 27 - strlen($env)) . "║\n";
echo "╚══════════════════════════════════════════╝\n\n";

// Tenta conectar (com retry)
$pdo = null;
for ($i = 1; $i <= 10; $i++) {
    try {
        $pdo = new PDO(
            "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",
            $c['user'], $c['pass'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "✅ Conectado ao banco [{$c['name']}]\n\n";
        break;
    } catch (Exception $e) {
        echo "⏳ Tentativa $i/10 - Aguardando banco...\n";
        sleep(3);
    }
}

if (!$pdo) {
    echo "❌ Não foi possível conectar ao banco após 10 tentativas.\n";
    exit(1);
}

// Tabela de controle de migrations
$pdo->exec("
    CREATE TABLE IF NOT EXISTS migrations (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        filename   VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
");

// Ler migrations já aplicadas
$applied = $pdo->query("SELECT filename FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

// Buscar arquivos SQL
$migrationsDir = __DIR__ . '/migrations';
$files = glob($migrationsDir . '/V*.sql');
sort($files);

if (empty($files)) {
    echo "⚠️  Nenhuma migration encontrada em db/migrations/\n";
    exit(0);
}

$count = 0;
foreach ($files as $file) {
    $filename = basename($file);

    if (in_array($filename, $applied)) {
        echo "⏭️  Já aplicada: $filename\n";
        continue;
    }

    echo "▶  Aplicando: $filename ... ";
    try {
        $sql = file_get_contents($file);
        $pdo->exec($sql);
        $stmt = $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)");
        $stmt->execute([$filename]);
        echo "✅\n";
        $count++;
    } catch (Exception $e) {
        echo "❌ ERRO: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "\n✅ $count migration(s) aplicada(s) com sucesso!\n";
