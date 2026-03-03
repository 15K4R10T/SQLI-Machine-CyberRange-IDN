# SQLI-Machine-CyberRange-IDN

> Mesin lab SQL Injection berbasis Docker untuk keperluan edukasi dan pelatihan keamanan siber — bagian dari ekosistem **ID-Networkers CyberRange**.

![Docker](https://img.shields.io/badge/Docker-Single%20Container-2496ED?logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)
![Platform](https://img.shields.io/badge/Platform-Ubuntu%2020.04-E95420?logo=ubuntu&logoColor=white)
![License](https://img.shields.io/badge/Use-Educational%20Only-e63946)

---

## Daftar Isi

- [Tentang Lab](#tentang-lab)
- [Struktur Repository](#struktur-repository)
- [Arsitektur](#arsitektur)
- [Modul Lab](#modul-lab)
- [Cara Menjalankan](#cara-menjalankan)
- [Commands](#commands)
- [Disclaimer](#disclaimer)

---

## Tentang Lab

Lab ini menyediakan lingkungan SQL Injection yang terisolasi dan siap pakai dalam **satu Docker container**. Dirancang untuk pelatihan keamanan siber tingkat enterprise, dengan tiga modul yang mencakup teknik dari dasar hingga lanjutan.

---

## Struktur Repository

```
SQLI-Machine-CyberRange-IDN/
├── lab-sqli-single/          # Source code lengkap
│   ├── Dockerfile            # Build single container
│   ├── entrypoint.sh         # Init MySQL + start services
│   ├── supervisord.conf      # Process manager (Apache + MySQL)
│   ├── apache.conf           # Konfigurasi virtual host
│   ├── init.sql              # Database schema + data dummy
│   ├── run.sh                # Script build & run otomatis
│   └── web/                  # Aplikasi PHP
│       ├── index.php         # Homepage / Dashboard
│       ├── basic/            # Modul 1 - Basic SQLi
│       ├── auth/             # Modul 2 - Auth Bypass
│       ├── blind/            # Modul 3 - Blind SQLi
│       ├── includes/         # Database connection helper
│       └── assets/           # CSS & static files
└── lab-sqli-single.tar.gz   # Pre-built archive (siap extract)
```

---

## Arsitektur

```
Docker Container: lab-sqli
├── Supervisor (process manager)
│   ├── Apache 2 + PHP 8.1  →  port 8080
│   └── MySQL 8.0           →  internal only (tidak expose)
└── /var/www/html/
    ├── /basic/
    ├── /auth/
    └── /blind/
```

---

## Modul Lab

| # | Modul | Path | Tingkat | Teknik |
|---|-------|------|---------|--------|
| 1 | Basic SQL Injection | `/basic/` | Easy | Error-based, UNION-based |
| 2 | Auth Bypass + Filtering | `/auth/` | Medium | Login bypass, filter evasion (3 level) |
| 3 | Blind / Logic-Based SQLi | `/blind/` | Hard | Boolean-based, Time-based |

---

## Cara Menjalankan

### Prasyarat

- Ubuntu 20.04 / Debian / VM Proxmox
- Docker terinstall

```bash
# Install Docker jika belum ada
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
newgrp docker
```

---

### Opsi A — Build dari Source Code

```bash
# 1. Clone repository
git clone https://github.com/15K4R10T/SQLI-Machine-CyberRange-IDN.git

# 2. Masuk ke folder
cd SQLI-Machine-CyberRange-IDN/lab-sqli-single

# 3. Jalankan
chmod +x run.sh
./run.sh
```

---

### Opsi B — Dari File Archive (.tar.gz)

```bash
docker load < lab-sqli-image.tar.gz
docker run -d --name lab-sqli -p 8080:80 --restart unless-stopped lab-sqli
```

---

### Akses Lab

Setelah container berjalan, buka browser:

```
http://localhost:8080          # Jika dijalankan di mesin lokal
http://<IP-VM>:8080            # Jika dijalankan di VM / server
```

---

## Commands

```bash
# Cek status container
docker ps

# Lihat log real-time
docker logs -f lab-sqli

# Stop lab
docker stop lab-sqli

# Start ulang lab
docker start lab-sqli

# Masuk ke dalam container (debug)
docker exec -it lab-sqli bash

# Hapus container & rebuild dari awal
docker stop lab-sqli && docker rm lab-sqli
cd lab-sqli-single && ./run.sh
```

---

## Disclaimer

> Lab ini dibuat **hanya untuk keperluan edukasi dan pelatihan keamanan siber** di lingkungan yang terisolasi.  
> Jangan gunakan teknik yang dipelajari pada sistem atau jaringan tanpa izin tertulis dari pemiliknya.  
> ID-Networkers tidak bertanggung jawab atas penyalahgunaan materi dalam repositori ini.

---

<div align="center">
  <sub>Made by akmal <strong>ID-Networkers</strong> — Indonesian IT Expert Factory</sub>
</div>
