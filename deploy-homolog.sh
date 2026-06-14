#!/bin/bash
# =====================================================
# F) Atualização do ambiente de Homologação
# Uso: ./deploy-homolog.sh
# =====================================================

set -e

echo "╔══════════════════════════════════════════╗"
echo "║   DEPLOY - AMBIENTE DE HOMOLOGAÇÃO       ║"
echo "╚══════════════════════════════════════════╝"

# 1. Garantir código atualizado
echo ""
echo "▶ [1/4] Atualizando código do repositório..."
git pull origin main

# 2. Build da imagem com a tag homolog
echo ""
echo "▶ [2/4] Construindo imagem Docker (homolog)..."
docker build -t gcsoft-app:homolog .

# 3. Subir/recriar containers
echo ""
echo "▶ [3/4] Subindo containers de Homologação..."
cd docker/homolog
docker compose down --remove-orphans
docker compose up -d

# 4. Aguardar e rodar migrations
echo ""
echo "▶ [4/4] Aguardando banco e rodando migrations (Flyway)..."
sleep 5
docker compose run --rm flyway-homolog

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║  ✅ Homologação atualizada com sucesso!  ║"
echo "║  🌐 Acesse: http://177.44.248.89:8081    ║"
echo "╚══════════════════════════════════════════╝"
