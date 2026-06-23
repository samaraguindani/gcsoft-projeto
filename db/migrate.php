<?php
$env = $argv[1] ?? 'homolog';

$config = [
    'homolog' => ['host'=>'127.0.0.1','port'=>'3307','name'=>'gcsoft_homolog','user'=>'gcsoft','pass'=>'gcsoft123'],
    'prod'    => ['host'=>'127.0.0.1','port'=>'3307','name'=>'gcsoft_prod',   'user'=>'gcsoft','pass'=>'gcsoft123'],
];

if (!isset($config[$env])) { echo "Uso: php db/migrate.php homolog|prod\n"; exit(1); }

$c = $config[$env];
echo "MIGRATIONS - " . strtoupper($env) . "\n";

$pdo = null;
for ($i = 1; $i <= 10; $i++) {
    try {
        $pdo = new PDO("mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4", $c['user'], $c['pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
        echo "Conectado ao banco [{$c['name']}]\n";
        break;
    } catch (Exception $e) {
        echo "Tentativa $i/10 - Aguardando banco...\n";
        sleep(3);
    }
}

if (!$pdo) { echo "Erro: nao foi possivel conectar.\n"; exit(1); }

$pdo->exec("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) NOT NULL UNIQUE, applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP)");

$applied = $pdo->query("SELECT filename FROM migrations")->fetchAll(PDO::FETCH_COLUMN);
$files = glob(__DIR__ . '/migrations/V*.sql');
sort($files);

$count = 0;
foreach ($files as $file) {
    $filename = basename($file);
    if (in_array($filename, $applied)) { echo "Ja aplicada: $filename\n"; continue; }
    echo "Aplicando: $filename ... ";
    try {
        $pdo->exec(file_get_contents($file));
        $pdo->prepare("INSERT INTO migrations (filename) VALUES (?)")->execute([$filename]);
        echo "OK\n";
        $count++;
    } catch (Exception $e) {
        echo "ERRO: " . $e->getMessage() . "\n";
        exit(1);
    }
}

echo "$count migration(s) aplicada(s)!\n";
