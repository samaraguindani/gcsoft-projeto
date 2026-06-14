# GCSoft — Gerência de Configuração de Software

**Disciplina:** 4815207 – Gerência de Configuração de Software  
**Prof.:** Fabrício | 2026/A

---

## 🏗️ Arquitetura

| Camada | Ferramenta |
|---|---|
| Controle de Mudança | GitHub Issues |
| Versionamento | Git + GitHub |
| Integração (CI) | GitHub Actions |
| Testes Automatizados | PHPUnit 10 (20 testes) |
| Qualidade de Código | PHP CodeSniffer (PSR-12) |
| Versionamento de BD | Flyway |
| Containers | Docker + Docker Compose |
| Aplicação | PHP 8.2 + Apache |
| Banco de Dados | MySQL 8.0 |
| Linguagem | PHP 8.2 |

## 🌐 Ambientes

| Ambiente | URL | Porta App | Porta MySQL |
|---|---|---|---|
| Homologação | http://177.44.248.89:8081 | 8081 | 3307 |
| Produção | http://177.44.248.89:8082 | 8082 | 3308 |

---

## 🚀 Setup Inicial na VM

```bash
# 1. Instalar dependências
sudo apt update && sudo apt upgrade -y
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER && newgrp docker
sudo apt install -y git php php-cli php-mbstring php-xml php-zip unzip curl
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# 2. Clonar o projeto
git clone https://github.com/SEU_USUARIO/gcsoft-projeto.git
cd gcsoft-projeto
composer install

# 3. Permissões dos scripts
chmod +x deploy-homolog.sh deploy-prod.sh
```

---

## 📋 Pipeline CI/CD

### A) Registrar Mudança
- Abrir Issue no GitHub descrevendo a mudança

### B) Implementar
- Criar branch: `git checkout -b feature/nome-da-feature`
- Editar código em `src/` e migrations em `db/migrations/`

### C) Versionar
```bash
git add .
git commit -m "feat: descrição da mudança"
git push origin feature/nome-da-feature
```
- Abrir Pull Request no GitHub

### D+E) Integração automática (GitHub Actions)
- Ao fazer push/PR, o pipeline executa automaticamente:
  - 20 testes automatizados (PHPUnit)
  - Estatísticas de execução
  - Análise de qualidade PSR-12
  - Build da imagem Docker

### F) Atualizar Homologação
```bash
./deploy-homolog.sh
```

### G) Atualizar Produção
```bash
./deploy-prod.sh
```

---

## 🧪 Executar testes localmente

```bash
mkdir -p reports
composer install
vendor/bin/phpunit --testdox
```
