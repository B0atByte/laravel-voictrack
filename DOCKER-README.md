# VoiceTrackBpl - Docker Setup Guide

ระบบจัดการไฟล์เสียง VoiceTrackBpl บน Docker พร้อม phpMyAdmin

## 📋 ข้อกำหนดระบบ

- Docker Desktop (Windows/Mac/Linux)
- Docker Compose
- อย่างน้อย 2GB RAM
- อย่างน้อย 5GB พื้นที่ว่าง

## 🚀 วิธีรันระบบ

### 1. ติดตั้ง Docker Desktop

ดาวน์โหลดและติดตั้งจาก: https://www.docker.com/products/docker-desktop

### 2. เตรียมไฟล์ .env

```bash
# คัดลอก .env สำหรับ Docker
copy .env.docker .env
```

หรือบน Linux/Mac:
```bash
cp .env.docker .env
```

### 3. Build และรัน Docker Containers

```bash
docker-compose up -d --build
```

คำสั่งนี้จะ:
- Build Docker images
- สร้างและรัน containers ทั้งหมด
- รันในโหมด background (-d)

### 4. รัน Database Migrations

```bash
docker-compose exec app php artisan migrate --seed
```

### 5. สร้าง Admin User (ถ้ายังไม่มี)

```bash
docker-compose exec app php artisan db:seed --class=AdminSeeder
```

## 🌐 เข้าใช้งานระบบ

### URLs สำคัญ:

- **เว็บไซต์หลัก:** http://localhost:8086
- **phpMyAdmin:** http://localhost:8090
- **Admin Login:** http://localhost:8086/login

### ข้อมูล Login:

**Admin Panel:**
- Username: `admin`
- Password: `admin123`

**phpMyAdmin:**
- Server: `db`
- Username: `root`
- Password: `root`

**หรือ:**
- Username: `voicetrack`
- Password: `voicetrack123`

## 📦 Docker Services

| Service | Port | Description |
|---------|------|-------------|
| **app** | - | PHP 8.2-FPM (Laravel) |
| **webserver** | 8086 | Nginx Web Server |
| **db** | 3308 | MySQL 8.0 Database |
| **phpmyadmin** | 8090 | phpMyAdmin Interface |

## 🛠️ คำสั่งที่มีประโยชน์

### ดู Logs

```bash
# ดู logs ทั้งหมด
docker-compose logs

# ดู logs แบบ real-time
docker-compose logs -f

# ดู logs เฉพาะ service
docker-compose logs app
docker-compose logs db
```

### เข้าไปใน Container

```bash
# เข้าไปใน Laravel container
docker-compose exec app bash

# เข้าไปใน MySQL container
docker-compose exec db bash
```

### รัน Artisan Commands

```bash
# รัน migration
docker-compose exec app php artisan migrate

# Clear cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear

# สร้าง admin user ใหม่
docker-compose exec app php artisan db:seed --class=AdminSeeder
```

### หยุดและเริ่มระบบใหม่

```bash
# หยุดระบบ
docker-compose stop

# เริ่มระบบ
docker-compose start

# รีสตาร์ทระบบ
docker-compose restart

# หยุดและลบ containers
docker-compose down

# หยุดและลบ containers พร้อมข้อมูล
docker-compose down -v
```

## 🔧 การแก้ปัญหา

### ปัญหา: Port ชนกัน

ถ้า port 8080 หรือ 8081 ถูกใช้งานอยู่แล้ว แก้ไขใน `docker-compose.yml`:

```yaml
webserver:
  ports:
    - "9080:80"  # เปลี่ยนจาก 8080 เป็น 9080

phpmyadmin:
  ports:
    - "9081:80"  # เปลี่ยนจาก 8081 เป็น 9081
```

### ปัญหา: Permission denied

```bash
# ตั้งค่า permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### ปัญหา: Database connection refused

1. ตรวจสอบว่า MySQL container รันอยู่:
```bash
docker-compose ps
```

2. ตรวจสอบ logs:
```bash
docker-compose logs db
```

3. รอให้ MySQL พร้อมใช้งาน (ประมาณ 30-60 วินาที) แล้วรัน migration อีกครั้ง

### ปัญหา: ไฟล์ไม่แสดง

```bash
# ตรวจสอบว่า volume ถูก mount
docker-compose exec app ls -la storage/app/uploads
```

## 📊 Database Management

### เข้าถึง MySQL ผ่าน Command Line

```bash
docker-compose exec db mysql -u voicetrack -pvoicetrack123 voictrack_db
```

### Backup Database

```bash
docker-compose exec db mysqldump -u voicetrack -pvoicetrack123 voictrack_db > backup.sql
```

### Restore Database

```bash
docker-compose exec -T db mysql -u voicetrack -pvoicetrack123 voictrack_db < backup.sql
```

## 🔒 ความปลอดภัย

**สำหรับ Production:**

1. เปลี่ยนรหัสผ่าน database ใน `docker-compose.yml` และ `.env`
2. เปลี่ยน `APP_DEBUG=false` ใน `.env`
3. ตั้ง `APP_ENV=production`
4. Generate APP_KEY ใหม่:
```bash
docker-compose exec app php artisan key:generate
```

## 📝 หมายเหตุ

- ข้อมูล database จะถูกเก็บใน Docker volume `db-data`
- ไฟล์ uploads จะถูกเก็บใน `storage/app/uploads`
- เมื่อรัน `docker-compose down -v` จะลบข้อมูลทั้งหมด

## 🆘 การขอความช่วยเหลือ

หากพบปัญหาในการใช้งาน:

1. ตรวจสอบ logs: `docker-compose logs`
2. ตรวจสอบสถานะ containers: `docker-compose ps`
3. รีสตาร์ทระบบ: `docker-compose restart`

---

**เอกสารนี้อัปเดตล่าสุด:** 6 มกราคม 2026
