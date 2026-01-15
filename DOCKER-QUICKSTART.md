# 🚀 VoiceTrackBpl - Docker Quick Start

## วิธีเริ่มต้นอย่างรวดเร็ว

### Windows:
```cmd
docker-start.bat
```

### Linux/Mac:
```bash
chmod +x docker-start.sh
./docker-start.sh
```

## 🌐 URLs

| Service | URL |
|---------|-----|
| Website | http://localhost:8086 |
| phpMyAdmin | http://localhost:8090 |
| Admin Login | http://localhost:8086/login |

## 🔑 Login Info

**Admin:**
- Username: `admin`
- Password: `admin123`

**phpMyAdmin:**
- Server: `db`
- Username: `root` / Password: `root`
- หรือ Username: `voicetrack` / Password: `voicetrack123`

## ⚡ คำสั่งที่ใช้บ่อย

```bash
# เริ่มระบบ
docker-compose up -d

# หยุดระบบ
docker-compose stop

# รีสตาร์ท
docker-compose restart

# ดู logs
docker-compose logs -f

# เข้าไปใน container
docker-compose exec app bash

# รัน migration
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear

# หยุดและลบทั้งหมด
docker-compose down

# หยุดและลบรวมข้อมูล
docker-compose down -v
```

## 🛠️ Troubleshooting

**MySQL ยังไม่พร้อม:**
```bash
# รอ 30 วินาทีแล้วลองใหม่
docker-compose exec app php artisan migrate
```

**Permission ไม่ถูกต้อง:**
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

**ต้องการรีเซ็ตทุกอย่าง:**
```bash
docker-compose down -v
docker-compose up -d --build
# รอ 30 วินาที
docker-compose exec app php artisan migrate --seed
```

## 📝 Notes

- Database data เก็บใน Docker volume
- Uploads เก็บใน `storage/app/uploads`
- ใช้ `docker-compose down -v` จะลบข้อมูลทั้งหมด (ระวัง!)
