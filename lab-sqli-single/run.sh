#!/bin/bash
# run.sh — Satu command untuk build + run Lab SQLi
# Usage: ./run.sh

set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

IMAGE_NAME="lab-sqli"
CONTAINER_NAME="lab-sqli"
PORT="8080"

echo -e "${CYAN}"
echo "╔══════════════════════════════════════╗"
echo "║      Lab SQLi — Single Container     ║"
echo "╚══════════════════════════════════════╝"
echo -e "${NC}"

# Cek Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}[✗] Docker tidak ada! Install dulu:${NC}"
    echo "    curl -fsSL https://get.docker.com | sh"
    echo "    sudo usermod -aG docker \$USER && exit"
    exit 1
fi

# Stop & hapus container lama jika ada
if docker ps -a --format '{{.Names}}' | grep -q "^${CONTAINER_NAME}$"; then
    echo -e "${YELLOW}[*] Menghapus container lama...${NC}"
    docker stop $CONTAINER_NAME 2>/dev/null || true
    docker rm $CONTAINER_NAME 2>/dev/null || true
fi

# Build image
echo -e "${YELLOW}[*] Building Docker image (ini butuh 1-2 menit pertama kali)...${NC}"
docker build -t $IMAGE_NAME .

# Jalankan container
echo -e "${YELLOW}[*] Menjalankan container...${NC}"
docker run -d \
    --name $CONTAINER_NAME \
    -p ${PORT}:80 \
    --restart unless-stopped \
    $IMAGE_NAME

# Tunggu service siap
echo -e "${YELLOW}[*] Menunggu MySQL + Apache siap...${NC}"
for i in $(seq 1 20); do
    if curl -s -o /dev/null -w "%{http_code}" http://localhost:$PORT 2>/dev/null | grep -q "200"; then
        break
    fi
    printf "   Starting... (%d/20)\r" $i
    sleep 2
done

echo ""

# Hasil akhir
VM_IP=$(hostname -I | awk '{print $1}')

echo -e "${GREEN}"
echo "╔══════════════════════════════════════════════════╗"
echo "║  ✅  Lab SQLi berhasil dijalankan!               ║"
echo "╠══════════════════════════════════════════════════╣"
echo "║  🌐  Buka di browser:                            ║"
printf "║      http://%-36s║\n" "${VM_IP}:${PORT}"
echo "║                                                  ║"
echo "║  📦  Modul:                                      ║"
echo "║      /basic/  → Basic SQLi (Easy)                ║"
echo "║      /auth/   → Auth Bypass (Medium)             ║"
echo "║      /blind/  → Blind SQLi (Hard)                ║"
echo "╠══════════════════════════════════════════════════╣"
echo "║  🛠️   Commands:                                   ║"
echo "║      docker logs -f lab-sqli   (lihat log)       ║"
echo "║      docker stop lab-sqli      (stop)            ║"
echo "║      docker start lab-sqli     (start ulang)     ║"
echo "╚══════════════════════════════════════════════════╝"
echo -e "${NC}"
