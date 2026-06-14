#!/bin/bash
# =====================================================
# G) Atualização do ambiente de Produção
# Uso: ./deploy-prod.sh
# =====================================================

set -e

echo "╔══════════════════════════════════════════╗"
echo "║   DEPLOY - AMBIENTE DE PRODUÇÃO          ║"
echo "╚══════════════════════════════════════════╝"

# Confirmação de segurança
echo ""
read -p "⚠️  Você tem certeza que deseja atualizar PRODUÇÃO? (s/N): " confirm
if [[ "$confirm" != "s" && "$confirm" != "S" ]]; then
    echo "❌ Deploy cancelado."
    exit 0
fi

# 1. Garantir código atualizado
echo ""
echo "▶ [1/4] Atualizando código do repositório..."
git pull origin main

# 2. Build da imagem com a tag prod
echo ""
echo "▶ [2/4] Construindo imagem Docker (prod)..."
docker build -t gcsoft-app:prod .

# 3. Subir/recriar containers
echo ""
echo "▶ [3/4] Subindo containers de Produção..."
cd docker/prod
docker compose down --remove-orphans
docker compose up -d

# 4. Aguardar e rodar migrations
echo ""
echo "▶ [4/4] Aguardando banco e rodando migrations (Flyway)..."
sleep 5
docker compose run --rm flyway-prod

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║  ✅ Produção atualizada com sucesso!     ║"
echo "║  🌐 Acesse: http://177.44.248.89:8082    ║"
echo "╚══════════════════════════════════════════╝"
