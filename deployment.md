# Deployment Guide - Production Server (Unified)

Ikuti langkah-langkah berikut untuk melakukan deployment proyek **Logbook Digital Server Room** di server produksi Anda menggunakan Docker. Konfigurasi ini identik dengan apa yang Anda jalankan di komputer lokal.

## Prasyarat
- Server dengan OS Linux (disarankan Ubuntu 22.04+).
- Sudah terinstall **Docker** dan **Docker Compose**.
- Akses jaringan untuk melakukan `git clone`.

## 1. Persiapan Folder Proyek
Clone repository dari GitHub ke server Anda:
```bash
git clone https://github.com/MhusnulZ/logbook-server.git
cd logbook-server
```

## 2. Konfigurasi Environment (`.env`)
Salin file example dan sesuaikan pengaturannya untuk produksi:
```bash
cp .env.example .env
nano .env
```
**Sesuaikan bagian penting berikut:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=http://alamat-ip-server-atau-domain`
- `DB_HOST=db`
- `DB_PASSWORD=buat-password-kuat-disini`

## 3. Menjalankan Docker
Gunakan perintah standar Docker Compose:
```bash
docker-compose up -d --build
```

## 4. Optimasi & Migrasi Database
Jalankan perintah ini di dalam kontainer untuk menyiapkan database dan mengoptimalkan sistem:
```bash
# Generate App Key (jika baru pertama kali)
docker-compose exec app php artisan key:generate

# Jalankan migrasi database
docker-compose exec app php artisan migrate --force

# Optimasi konfigurasi, route, dan view
docker-compose exec app php artisan optimize
```

## 5. Verifikasi
Akses server Anda melalui browser:
- **Lokal**: `http://localhost:8080`
- **Produksi**: `http://alamat-ip-server-anda` (Sesuaikan mapping port di docker-compose jika perlu).

---

## Tips Maintenance
- **Perbarui Kode**: `git pull origin main` lalu jalankan `docker-compose up -d --build`.
- **Cek Log**: `docker-compose logs -f app`.
- **Hapus Data**: Gunakan `docker-compose down -v` jika ingin menghapus seluruh volume (Hati-hati!).

> [!CAUTION]
> Selalu backup volume `logbook_db_data` secara berkala untuk menjaga keamanan data database Anda.
