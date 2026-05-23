# Kamus Data & Keputusan Arsitektur

## Ringkasan

Dokumen ini menjelaskan struktur khusus dan tipe data krusial yang digunakan dalam skema database Storymoon untuk mencapai standar Enterprise-grade.

## Daftar Isi

1. [Identifikasi Unik (Primary Keys)](#identifikasi-unik-primary-keys)
2. [Pelindung Barang Bukti (SoftDeletes)](#pelindung-barang-bukti-softdeletes)
3. [Pipeline Status (Enum)](#pipeline-status-enum)

---

## Identifikasi Unik (Primary Keys)

Platform ini membedakan penggunaan tipe Primary Key berdasarkan volume pertumbuhan data:

### BigInt Auto-Increment

```
Tabel: users, wallets
Tipe: BIGINT AUTO_INCREMENT
```

**Alasan:**

- Data tumbuh secara linear dan sering digunakan untuk relasi statis
- Kecepatan pencarian (indexing) sangat diutamakan
- Cocok untuk tabel master dengan jumlah baris terbatas

### ULID (Universally Unique Lexicographically Sortable Identifier)

```
Tabel: novels, chapters, transactions, chapter_purchases, dll.
Tipe: CHAR(26) atau BINARY(16)
```

**Alasan:**

- Tabel transaksional akan tumbuh eksponensial (jutaan baris)
- ULID lebih aman dari UUID karena bisa diurutkan berdasarkan waktu (_time-sortable_)
- Mencegah database fragmentation
- Mengamankan ID dari tebakan pengguna (ID URL tidak tertebak)
- Meningkatkan keamanan dan privasi pengguna

---

## Pelindung Barang Bukti (SoftDeletes)

### Konsep

Menggunakan kolom `deleted_at` (Timestamp). Jika ada isinya, data dianggap "terhapus" oleh sistem, tapi fisik datanya masih ada di database.

### Tabel yang Menggunakan SoftDelete

- `novels`
- `chapters`
- `contracts`
- `reviews`
- `comments`

### Alasan Implementasi

- **Keamanan Hukum**: Mencegah hilangnya barang bukti saat terjadi review bombing, sengketa kontrak, atau eskalasi moderasi
- **Recovery**: Admin tetap bisa merestorasi (mengembalikan) data yang tidak sengaja terhapus
- **Audit Trail**: Mempertahankan riwayat lengkap untuk keperluan audit dan compliance
- **Data Integrity**: Menjaga integritas referensi foreign key

---

## Pipeline Status (Enum)

Sistem ini menghindari penggunaan tipe `boolean` (`is_approved`) pada tabel yang memiliki alur kerja panjang, dan menggantinya dengan `ENUM` Pipeline yang terstruktur.

### Kontrak (Contracts)

**Status Pipeline:**

```
text_review → kyc_submission → kyc_review → signing → active
```

**Deskripsi:**

- Mendukung Progressive Onboarding
- Memungkinkan reversing ke tahap sebelumnya jika ada perubahan
- Tracking status lengkap untuk audit

### Laporan Novel (Novel Reports)

**Status Pipeline:**

```
pending → reviewed → resolved / escalated
```

**Deskripsi:**

- Mendukung eskalasi bertingkat ke Admin
- Memungkinkan multiple resolution paths

### Penarikan Dana (Withdrawals)

**Status Pipeline:**

```
pending → approved / rejected
```

**Deskripsi:**

- Status biner dengan jelas
- Memudahkan tracking untuk laporan keuangan
