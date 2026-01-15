# VoiceTrack - ระบบจัดการไฟล์เสียง

<p align="center">
  <strong>ระบบจัดการและแชร์ไฟล์เสียงแบบ Web-based</strong>
</p>

---

## 📋 คำอธิบายโปรเจ็ค

VoiceTrack เป็นระบบจัดการไฟล์เสียง (Audio Files) ที่สร้างขึ้นด้วย **Laravel 11** ให้สามารถ:

✨ **คุณสมบัติหลัก:**
- 📤 **อัปโหลดไฟล์เสียง** - อัปโหลดไฟล์เสียงจากแบบฟอร์ม
- 🔍 **ค้นหาไฟล์** - ค้นหาไฟล์เสียงตามรหัสหรือผู้ร้องขอ
- 📥 **ดาวน์โหลด** - ดาวน์โหลดไฟล์เดี่ยวหรือแบบชุด
- 🔊 **สตรีมมิ่ง** - ฟังไฟล์เสียงผ่าน Browser
- 📅 **วันหมดอายุ** - กำหนดวันหมดอายุสำหรับไฟล์
- 👨‍💼 **ระบบ Admin** - จัดการไฟล์และผู้ใช้

---

## 🛠️ เทคโนโลยีที่ใช้

- **Backend:** Laravel 11
- **Frontend:** Blade Templates, JavaScript, Vite
- **Database:** MySQL
- **Server:** Nginx
- **Docker:** Docker & Docker Compose (สำหรับการพัฒนา)

---

## 📦 โครงสร้างฐานข้อมูล

### 👥 Users
- ระบบผู้ใช้งานทั่วไป

### 🔐 Admins
- `username` - ชื่อผู้ใช้ (สำหรับ login admin)
- `password` - รหัสผ่าน (เข้ารหัส)

### 🎵 Files
- `code` - รหัสไฟล์ (สำหรับค้นหา)
- `filename` - ชื่อไฟล์เสียง
- `requester` - ชื่อผู้ร้องขอ
- `expiry_date` - วันหมดอายุ
- `uploaded_at` - วันที่อัปโหลด

---

## 🚀 การ Setup และการรัน

### วิธี 1: ใช้ Docker (แนะนำ)

ดูรายละเอียดใน [DOCKER-README.md](DOCKER-README.md)

```bash
docker-compose up -d --build
docker-compose exec app php artisan migrate --seed
```

### วิธี 2: รันบนเครื่องท้องถิ่น

#### ข้อกำหนด:
- PHP 8.2 ขึ้นไป
- Composer
- MySQL / SQLite

#### ขั้นตอน:

1. **ติดตั้ง Dependencies:**
```bash
composer install
npm install
```

2. **ตั้งค่า Environment:**
```bash
copy .env.example .env
php artisan key:generate
```

3. **ตั้งค่าฐานข้อมูล:**
```bash
php artisan migrate --seed
```

4. **รัน Development Server:**
```bash
php artisan serve
npm run dev
```

5. **เข้าใช้งาน:**
- 🏠 ไซต์หลัก: http://localhost:8000
- 🔒 Admin Login: http://localhost:8000/login

---

## 🔑 Credentials เริ่มต้น

**Admin User (หลังจากรัน seed):**
- Username: `admin`
- Password: `password`

---

## 📁 โครงสร้างโฟลเดอร์

```
laravel-voictrack/
├── app/
│   ├── Http/Controllers/      # Controller (PublicController, AdminController, AuthController)
│   └── Models/                # Eloquent Models (User, Admin, File)
├── database/
│   ├── migrations/            # Database Schema
│   └── seeders/               # Database Seeders
├── resources/
│   ├── views/                 # Blade Templates
│   ├── css/                   # Stylesheets
│   └── js/                    # JavaScript
├── routes/                    # Route Definitions
├── storage/
│   └── app/uploads/           # โฟลเดอร์เก็บไฟล์เสียง
└── tests/                     # Unit & Feature Tests
```

---

## 🛣️ Routes (เส้นทาง)

### Public Routes:
- `GET /` - หน้าแรก (ค้นหาและดาวน์โหลด)
- `POST /search` - ค้นหาไฟล์
- `GET /download/{id}` - ดาวน์โหลดไฟล์เดี่ยว
- `GET /download-all/{code}` - ดาวน์โหลดไฟล์แบบชุด
- `GET /stream/{id}` - สตรีมมิ่งไฟล์เสียง

### Authentication Routes:
- `GET /login` - ฟอร์ม Login
- `POST /login` - ยืนยัน Login
- `POST /logout` - ออกจากระบบ

### Admin Routes (ต้องเข้าสู่ระบบ):
- `GET /admin/dashboard` - แดชบอร์ด Admin
- `POST /admin/upload` - อัปโหลดไฟล์
- `DELETE /admin/delete/{id}` - ลบไฟล์เดี่ยว
- `POST /admin/delete-all` - ลบไฟล์หลายรายการ

---

## 🧪 Testing

รัน Unit Tests:
```bash
php artisan test
```

---

## 📝 Seeders

### AdminSeeder
```bash
php artisan db:seed --class=AdminSeeder
```
สร้าง Admin User เริ่มต้น (username: `admin`, password: `password`)

### MigrateOldDataSeeder
```bash
php artisan db:seed --class=MigrateOldDataSeeder
```
โอนย้ายข้อมูลเก่าจากระบบเดิม

---

## 🐛 Troubleshooting

### ปัญหา: ไม่สามารถเข้าสู่ระบบได้
```bash
# Reset ฐานข้อมูล
php artisan migrate:refresh --seed
```

### ปัญหา: ไม่เห็นไฟล์อัปโหลด
ตรวจสอบสิทธิ์ของโฟลเดอร์:
```bash
chmod -R 775 storage/
```

### Docker ไม่ทำงาน
ดูรายละเอียดเพิ่มเติมใน [DOCKER-QUICKSTART.md](DOCKER-QUICKSTART.md)

---

## 📞 ติดต่อและสนับสนุน

สำหรับปัญหาการใช้งาน โปรดติดต่อทีมพัฒนา

---

## 📄 License

MIT License - เปิดเผยให้ใช้งานอย่างอิสระ
