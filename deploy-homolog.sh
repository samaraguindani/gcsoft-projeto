#!/bin/bash
set -e

echo "╔══════════════════════════════════════════╗"
echo "║   DEPLOY - AMBIENTE DE HOMOLOGAÇÃO       ║"
echo "╚══════════════════════════════════════════╝"

echo ""
echo "▶ [1/4] Atualizando código do repositório..."
git pull origin main

echo ""
echo "▶ [2/4] Construindo imagem Docker (homolog)..."
sudo docker build -t gcsoft-app:homolog .

echo ""
echo "▶ [3/4] Subindo containers de Homologação..."
cd docker/homolog
sudo docker compose down --remove-orphans 2>/dev/null || true
sudo docker compose up -d
cd ../..

echo ""
echo "▶ [4/4] Rodando migrations no banco de Homologação..."
sleep 8
php db/migrate.php homolog

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║  ✅ Homologação atualizada com sucesso!  ║"
echo "║  🌐 Acesse: http://177.44.248.89:8081    ║"
echo "╚══════════════════════════════════════════╝"
