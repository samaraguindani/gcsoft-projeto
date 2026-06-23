#!/bin/bash
set -e

echo "╔══════════════════════════════════════════╗"
echo "║   DEPLOY - AMBIENTE DE PRODUÇÃO          ║"
echo "╚══════════════════════════════════════════╝"

read -p "⚠️  Confirma atualização de PRODUÇÃO? (s/N): " confirm
if [[ "$confirm" != "s" && "$confirm" != "S" ]]; then
    echo "❌ Deploy cancelado."
    exit 0
fi

echo ""
echo "▶ [1/4] Atualizando código do repositório..."
git pull origin main

echo ""
echo "▶ [2/4] Construindo imagem Docker (prod)..."
sudo docker build -t gcsoft-app:prod .

echo ""
echo "▶ [3/4] Subindo container de Produção..."
cd docker/prod
sudo docker compose down --remove-orphans 2>/dev/null || true
sudo docker compose up -d
cd ../..

echo ""
echo "▶ [4/4] Rodando migrations no banco de Produção..."
sleep 5
php db/migrate.php prod

echo ""
echo "╔══════════════════════════════════════════╗"
echo "║  ✅ Produção atualizada com sucesso!     ║"
echo "║  🌐 Acesse: http://177.44.248.89:8082    ║"
echo "╚══════════════════════════════════════════╝"
