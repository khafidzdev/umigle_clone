# Panduan Deploy Umingle (Laravel + Reverb + WebRTC) di Server Debian (Cockpit)

Mendeploy aplikasi yang menggunakan WebSockets (Reverb) dan Kamera (WebRTC) sedikit berbeda dengan website Laravel biasa. **Syarat MUTLAK yang harus dipenuhi adalah server Anda wajib menggunakan HTTPS (SSL)**, karena *browser* secara otomatis akan memblokir akses Kamera/Microphone jika koneksi bukan HTTPS.

Berikut adalah langkah-langkah lengkap (dari 0) untuk menjalankannya di Debian.

## 1. Persiapan Server
Pastikan server Debian Anda sudah terinstal perangkat lunak berikut:
- **PHP 8.2+** beserta ekstensinya (php-cli, php-fpm, php-mbstring, php-xml, php-curl, dll)
- **Composer**
- **Node.js & NPM**
- **Nginx** (Sangat disarankan dibanding Apache untuk menangani koneksi WebSockets)
- **Supervisor** (Untuk menjalankan Reverb di latar belakang)
- **Certbot / Let's Encrypt** (Untuk sertifikat SSL Gratis)

## 2. Kloning & Instalasi
Upload *source code* ini ke server (misalnya diletakkan di `/var/www/umingle`). Masuk ke folder tersebut lewat Terminal:
```bash
cd /var/www/umingle

# 1. Install dependencies PHP
composer install --optimize-autoloader --no-dev

# 2. Install dependencies Frontend & Build
npm install
npm run build

# 3. Atur Permissions
chown -R www-data:www-data /var/www/umingle
chmod -R 775 /var/www/umingle/storage
chmod -R 775 /var/www/umingle/bootstrap/cache
```

## 3. Konfigurasi `.env`
Gandakan `env.example` menjadi `.env`. Buka dan edit file `.env` untuk menyesuaikan mode produksi:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://umingle.domain-anda.com

# Database (biarkan file/sqlite jika tidak butuh DB)
DB_CONNECTION=sqlite

# Cache (Gunakan 'file' sesuai setup kita)
CACHE_STORE=file

# ================================
# KONFIGURASI REVERB & WEBSOCKETS
# ================================
REVERB_APP_ID=umingle_app
REVERB_APP_KEY=umingle_key_rahasia
REVERB_APP_SECRET=umingle_secret_rahasia
REVERB_HOST="umingle.domain-anda.com"
REVERB_PORT=443
REVERB_SCHEME=https

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```
*Catatan: Jika `.env` diperbarui, jalankan `php artisan config:cache`.*

## 4. Konfigurasi Nginx & SSL
Server Nginx harus diatur menjadi dua tugas: 
1. Menyajikan website PHP biasa (HTTP/HTTPS).
2. Mem-*forward* jalur WebSocket (WSS) menuju port internal Laravel Reverb (port 8080).

Pertama, dapatkan SSL:
```bash
sudo certbot --nginx -d umingle.domain-anda.com
```

Kemudian edit blok konfigurasi Nginx (`/etc/nginx/sites-available/umingle`):
```nginx
server {
    listen 443 ssl;
    server_name umingle.domain-anda.com;
    root /var/www/umingle/public;
    index index.php index.html index.htm;

    # Konfigurasi SSL dari Certbot...
    ssl_certificate /etc/letsencrypt/live/umingle.domain-anda.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/umingle.domain-anda.com/privkey.pem;

    # 1. Menangani traffic PHP/Web biasa
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # 2. Proxy khusus ke Laravel Reverb (WebSockets)
    # Reverb secara default berjalan di localhost:8080
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header Host $host;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; # Sesuaikan versi PHP
    }
}
```
Lalu *restart* Nginx: `sudo systemctl restart nginx`.

## 5. Menjalankan Reverb dengan Supervisor
Reverb harus hidup 24/7 di latar belakang. Buat file konfigurasi Supervisor:
```bash
sudo nano /etc/supervisor/conf.d/reverb.conf
```
Isi dengan:
```ini
[program:reverb]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/umingle/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/umingle/storage/logs/reverb.log
```
Aktifkan prosesnya:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start reverb:*
```

## 6. Selesai!
Aplikasi YuhChat Anda sekarang dapat diakses secara *live* lewat jaringan internet global melalui domain HTTPS Anda. Semua lalu lintas video (WebRTC) dapat diakses dengan aman (browser tidak akan memblokir kamera), dan WebSocket akan diproses secara paralel oleh Nginx menuju Reverb!
