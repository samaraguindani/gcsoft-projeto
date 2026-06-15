-- Cria os dois databases no mesmo MySQL
CREATE DATABASE IF NOT EXISTS gcsoft_homolog CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE DATABASE IF NOT EXISTS gcsoft_prod    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Permissões para o usuário gcsoft em ambos
GRANT ALL PRIVILEGES ON gcsoft_homolog.* TO 'gcsoft'@'%';
GRANT ALL PRIVILEGES ON gcsoft_prod.*    TO 'gcsoft'@'%';
FLUSH PRIVILEGES;
