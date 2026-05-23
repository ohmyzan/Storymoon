# 📖 Storymoon Backend

Platform web novel modern yang mendukung ekosistem penulis dan pembaca dengan sistem monetisasi berbasis koin, manajemen kontrak eksklusif/non-eksklusif, dan kurasi konten berbasis komunitas.

**Dokumentasi Lengkap:** Lihat folder [docs/](./app/docs) untuk dokumentasi arsitektur, database, dan workflow.

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat-square&logo=php)](https://php.net)
[![License](https://img.shields.io/badge/License-MIT-green.svg?style=flat-square)](LICENSE)

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Prasyarat](#-prasyarat)
- [Instalasi](#-instalasi)
- [Konfigurasi](#-konfigurasi)
- [Penggunaan](#-penggunaan)
- [Dokumentasi](#-dokumentasi)
- [RBAC & Wewenang](#-matriks-wewenang-rbac-matrix)
- [Struktur Proyek](#-struktur-proyek)
- [Kontribusi](#-kontribusi)
- [Lisensi](#-lisensi)
- [Kontak](#-kontak)

### 🎯 Tautan Cepat Dokumentasi

Akses dokumentasi teknis dengan cepat:

- 📊 **Database:** [ERD](./app/docs/database/erd.md) | [Data Dictionary](./app/docs/database/data-dictionary.md)
- 🔧 **Architecture:** [Contract Workflow](./app/docs/architecture/contract-workflow.md) | [Moderation Workflow](./app/docs/architecture/moderation-workflow.md)
- 🔐 **Security:** [RBAC Matrix](./app/docs/security/rbac-matrix.md)

---

## 🚀 Fitur Utama

### 1. Multi-Divisional Backend (RBAC)

Sistem _Role-Based Access Control_ yang ketat dengan 5 divisi terpisah:

- **Super Admin:** Konfigurasi sistem global, manajemen staf, dan audit log tingkat tinggi.
- **Admin:** Manajemen operasional, validasi KYC (Legalitas), dan eskalasi kasus.
- **Finance:** Pengelolaan pencairan dana (Withdrawal) dan audit transaksi.
- **Editor:** Kurasi naskah, supervisi novel binaan, dan validasi bab.
- **Moderator:** Moderasi komentar dan pelaporan ulasan komunitas.

📖 **Lihat:** [RBAC Matrix Documentation](./app/docs/security/rbac-matrix.md)

### 2. Finansial & Monetisasi

- **Wallet System:** Pemisahan _source of truth_ antara saldo koin dan pendapatan rupiah penulis untuk mencegah _double-spending_.
- **Progressive Onboarding:** Alur kontrak yang dinamis (Eksklusif vs Non-Eksklusif) dengan sistem verifikasi data (KYC) yang progresif.
- **Automatic Ledger:** Perhitungan otomatis bagi hasil penulis dan platform yang langsung terekam saat bab dibeli.

📖 **Lihat:** [Contract Workflow Architecture](./app/docs/architecture/contract-workflow.md)

### 3. Sistem Moderasi Bertingkat

- **Two-Level Moderation:** Moderator menangani kasus ringan, Admin menangani eskalasi dan pemblokiran.
- **Community Reporting:** Pembaca dapat melaporkan konten toksik dengan sistem tiket tracking.
- **Soft Delete & Audit Trail:** Semua tindakan moderasi tercatat untuk compliance dan recovery.

📖 **Lihat:** [Moderation Workflow Architecture](./app/docs/architecture/moderation-workflow.md)

### 4. Arsitektur Scalable

- **ULID:** Penggunaan _Universally Unique Lexicographically Sortable Identifier_ pada tabel transaksional untuk memastikan performa database tetap optimal meskipun data mencapai jutaan baris.
- **Eager Loading:** Optimasi query untuk memusnahkan _N+1 Query Problem_ pada seluruh modul Filament.
- **Event-Driven:** Otomatisasi pembuatan dompet digital (Wallet) via _Observers_ dan _Queue Jobs_ untuk tugas latar belakang.

📖 **Lihat:** [Data Dictionary & Architecture Decisions](./app/docs/database/data-dictionary.md), [Entity Relationship Diagram](./app/docs/database/erd.md)

---

## 🛠 Tech Stack

| Komponen        | Teknologi                                                                 |
| --------------- | ------------------------------------------------------------------------- |
| Framework       | Laravel 12                                                                |
| Bahasa          | PHP 8.2+                                                                  |
| Interface Admin | FilamentPHP v3                                                            |
| Database        | MySQL 8.0+                                                                |
| Security        | Spatie Permission (RBAC), Google 2FA, Activity Log, Database Transactions |
| Build Tool      | Vite                                                                      |
| CSS Framework   | Tailwind CSS                                                              |

---

## 📋 Prasyarat

Sebelum menginstal, pastikan Anda memiliki:

- PHP 8.2 atau lebih tinggi
- Composer (latest version)
- MySQL 8.0 atau lebih tinggi
- Node.js 18+ dan npm/yarn
- Git

---

## 🔧 Instalasi

### Langkah 1: Clone Repository

```bash
git clone https://github.com/username/storymoon.git
cd storymoon
```

### Langkah 2: Install Dependencies

```bash
composer install
npm install
```

### Langkah 3: Setup Environment

```bash
cp .env.example .env
php artisan key:generate
```

### Langkah 4: Konfigurasi Database

Edit file `.env` dan sesuaikan konfigurasi database Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=storymoon
DB_USERNAME=root
DB_PASSWORD=
```

### Langkah 5: Migrasi Database

```bash
php artisan migrate:fresh --seed
```

---

## ⚙️ Konfigurasi

### Setup Storage Links

```bash
php artisan storage:link
```

### Generate Filament Admin User (jika diperlukan)

```bash
php artisan make:filament-user
```

### Konfigurasi Email

Edit file `.env` untuk mengatur SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@storymoon.local
```

---

## 🚀 Penggunaan

### Menjalankan Development Server

```bash
php artisan serve
npm run dev
```

Server akan berjalan di `http://localhost:8000`

### Build untuk Production

```bash
npm run build
php artisan optimize
```

### Testing

```bash
php artisan test
php artisan test --filter=NamaTest
```

---

## 📚 Dokumentasi

Untuk detail teknis lengkap tentang arsitektur sistem, alur kerja bisnis, dan struktur database, silakan merujuk ke dokumentasi yang tersedia:

### Arsitektur & Alur Kerja Bisnis

| Dokumentasi                                                           | Deskripsi                                                   |
| --------------------------------------------------------------------- | ----------------------------------------------------------- |
| [Contract Workflow](./app/docs/architecture/contract-workflow.md)     | Alur kerja pengajuan dan validasi kontrak (Progressive KYC) |
| [Moderation Workflow](./app/docs/architecture/moderation-workflow.md) | Sistem moderasi & eskalasi kedisiplinan pengguna            |
| [Security & RBAC](./app/docs/security/rbac-matrix.md)                 | Matriks wewenang akses tiap divisi                          |

### Struktur Database

| Dokumentasi                                               | Deskripsi                                         |
| --------------------------------------------------------- | ------------------------------------------------- |
| [Entity Relationship Diagram](./app/docs/database/erd.md) | Visualisasi hubungan entitas database             |
| [Data Dictionary](./app/docs/database/data-dictionary.md) | Penjelasan tipe data & keputusan arsitektur       |
| [Database Architecture](./app/docs/database/)             | Dokumentasi lengkap skema dan optimisasi database |

### Navigasi Dokumentasi

```
docs/
├── architecture/
│   ├── contract-workflow.md          # Alur kontrak eksklusif & non-eksklusif
│   ├── moderation-workflow.md        # Sistem moderasi bertingkat
│   └── ...
├── database/
│   ├── erd.md                        # Entity Relationship Diagram
│   ├── data-dictionary.md            # Kamus data & keputusan arsitektur
│   └── ...
└── security/
    └── rbac-matrix.md                # Matriks Role-Based Access Control
```

---

## 📂 Struktur Proyek

```
storymoon/
├── app/                          # Logika aplikasi
│   ├── Filament/                 # Admin panel modules
│   │   ├── Admin/
│   │   ├── Editor/
│   │   ├── Finance/
│   │   ├── Moderator/
│   │   └── SuperAdmin/
│   ├── Http/                     # Controllers & Middleware
│   ├── Models/                   # Eloquent Models
│   ├── Observers/                # Event Observers
│   ├── Policies/                 # Authorization Policies
│   └── docs/                     # Dokumentasi teknis
├── config/                       # Konfigurasi aplikasi
├── database/                     # Migrations & Seeds
├── routes/                       # Route definitions
├── resources/                    # Views & Assets
├── storage/                      # File storage
├── tests/                        # Unit & Feature tests
└── vendor/                       # Dependencies
```

---

## 🔐 Matriks Wewenang (RBAC Matrix)

Ringkasan akses berdasarkan role untuk setiap divisi:

| Fitur                 | Super Admin | Admin            | Finance | Editor | Moderator         |
| --------------------- | ----------- | ---------------- | ------- | ------ | ----------------- |
| **Kelola User**       | ✅          | ✅ (No Ban/Role) | ❌      | ❌     | ✅ (Mute/Suspend) |
| **Audit Logs**        | ✅          | ❌               | ❌      | ❌     | ❌                |
| **Validasi Kontrak**  | ✅          | ✅               | ❌      | ❌     | ❌                |
| **Review Naskah**     | ❌          | ❌               | ❌      | ✅     | ❌                |
| **Laporan Komunitas** | ✅          | ✅               | ❌      | ❌     | ✅                |

📖 **Lihat Detail Lengkap:**

- [RBAC Matrix Documentation](./app/docs/security/rbac-matrix.md) - Matriks wewenang detail
- [Moderation Workflow](./app/docs/architecture/moderation-workflow.md) - Alur moderasi & eskalasi
- [Contract Workflow](./app/docs/architecture/contract-workflow.md) - Alur validasi kontrak

---

## 🤝 Kontribusi

Kami menerima kontribusi dari komunitas! Untuk berkontribusi:

1. **Fork repository** ini
2. **Buat branch fitur** (`git checkout -b feature/AmazingFeature`)
3. **Commit perubahan Anda** (`git commit -m 'Add some AmazingFeature'`)
4. **Push ke branch** (`git push origin feature/AmazingFeature`)
5. **Buka Pull Request**

### Panduan Kontribusi

- Ikuti standar coding yang konsisten dengan project
- Tambahkan test untuk fitur baru
- Update dokumentasi sesuai perubahan
- Pastikan semua test lolos sebelum membuka PR

---

## 📝 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE). Lihat file [LICENSE](LICENSE) untuk detail.

---

## 💬 Kontak & Support

Jika Anda memiliki pertanyaan atau butuh bantuan:

- **Email:** support@storymoon.local
- **Issues:** [GitHub Issues](https://github.com/username/storymoon/issues)
- **Dokumentasi:** [Baca dokumentasi lengkap](./app/docs/)

---

**Made with ❤️ by Storymoon Team**
