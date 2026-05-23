# Arsitektur Alur Kerja Moderasi

## Ringkasan

Dokumen ini memetakan alur kerja sistem keamanan komunitas Storymoon yang mencakup moderasi Komentar, Ulasan, dan Novel. Sistem ini menggunakan arsitektur **Eskalasi Bertingkat** (Tiered Escalation) di mana:

- **Moderator** bertindak sebagai garda terdepan (menyembunyikan/menghapus konten)
- **Admin** bertindak sebagai eksekutor hukuman berat (pemblokiran akun)

## Daftar Isi

1. [Diagram Use Case](#diagram-use-case-aktor--hak-akses-moderasi)
2. [Diagram Aktivitas](#diagram-aktivitas-alur-penanganan-laporan-toksik)
3. [Diagram Urutan](#diagram-urutan-sistem-eskalasi--tombol-nuklir-admin)
4. [Fitur Utama](#fitur-utama)

---

## Diagram Use Case (Aktor & Hak Akses Moderasi)

Diagram ini menunjukkan pembagian wewenang yang sangat ketat (RBAC - Role-Based Access Control) antara berbagai aktor dalam sistem moderasi:

```mermaid
flowchart LR
    %% Aktor Utama
    Pembaca(["📖 Pembaca (Reporter)"])
    Moderator(["🛡️ Moderator (Garda Depan)"])
    Editor(["👓 Editor (Pengawas Novel)"])
    Admin(["⚖️ Admin (Eksekutor Hukuman)"])

    %% Use Cases Pembaca
    UC1("Lapor Komentar / Ulasan Toksik")
    UC2("Lapor Novel (Plagiat/Ilegal)")

    %% Use Cases Pegawai
    UC3("Review Laporan Komunitas")
    UC4("Sembunyikan Komentar (is_hidden)")
    UC5("Hapus Ulasan (SoftDelete)")
    UC6("Takedown Novel (Draft/Freeze)")
    UC7("Eskalasi Kasus Berat")
    UC8("Banned Akun Permanen (Hukuman Mati)")

    %% Relasi
    Pembaca --> UC1
    Pembaca --> UC2

    Moderator --> UC3
    Moderator --> UC4
    Moderator --> UC5
    Moderator --> UC7

    Editor --> UC2
    Editor --> UC7

    Admin --> UC6
    Admin --> UC8
    UC7 -. "Memicu" .-> Admin
```

---

## Diagram Aktivitas (Alur Penanganan Laporan Toksik)

Diagram ini menjelaskan alur keputusan ketika ada laporan yang masuk. Perhatikan bagaimana tindakan ringan diselesaikan di level Moderator, sementara tindakan berat (membutuhkan pemblokiran) dilempar ke Admin:

```mermaid
stateDiagram-v2
    [*] --> Laporan_Masuk : Pembaca melaporkan Ulasan/Komentar

    state "Kolam Laporan (Pending)" as Kolam_Laporan
    Laporan_Masuk --> Kolam_Laporan

    Kolam_Laporan --> Sidang_Moderator : Moderator membuka tiket laporan

    state Sidang_Moderator <<choice>>
    Sidang_Moderator --> Ditolak : Laporan Palsu (Aman)
    Sidang_Moderator --> Selesai_Ringan : Terbukti Pelanggaran Ringan
    Sidang_Moderator --> Eskalasi : Terbukti Pelanggaran Berat (Spam Bot / Hate Speech)

    Ditolak --> [*] : Kasus Ditutup

    Selesai_Ringan --> Eksekusi_Moderator : Moderator menghapus Ulasan / Menyembunyikan Komentar
    Eksekusi_Moderator --> [*] : Kasus Ditutup

    Eskalasi --> Meja_Admin : Laporan berpindah ke Panel Admin (Status: Escalated)

    Meja_Admin --> Sidang_Admin : Admin menginvestigasi barang bukti dari Moderator

    state Sidang_Admin <<choice>>
    Sidang_Admin --> Eksekusi_Ban : Harus di-Banned
    Sidang_Admin --> Eksekusi_Hapus : Cukup Dihapus

    Eksekusi_Ban --> Hapus_Konten : Update users.banned_at = NOW()
    Eksekusi_Hapus --> Hapus_Konten

    Hapus_Konten --> [*] : Kasus Ditutup Mutlak
```

---

## Diagram Urutan (Sistem Eskalasi & Tombol Nuklir Admin)

Diagram urutan ini menunjukkan bagaimana sistem mengelola alur data laporan antara Database, Panel Moderator, dan Panel Admin:

```mermaid
sequenceDiagram
    autonumber
    actor Reader as Pembaca (Reporter)
    participant API as Laravel API (Frontend)
    participant DB as MySQL Database
    actor Mod as Moderator
    actor Admin as Admin Platform

    Reader->>API: POST /api/reports/review {reason: "hate_speech"}
    API->>DB: INSERT into review_reports (status: pending)

    Note over Mod, DB: Level 1: Garda Depan (Panel Moderator)
    Mod->>API: Buka ReviewReportResource
    API-->>Mod: Tampilkan Laporan (Pending)
    Mod->>API: Evaluasi: Kasus Berat -> Klik "Eskalasi ke Admin"
    API->>DB: UPDATE review_reports SET status = 'escalated', mod_notes = "Spam berulang"
    API-->>Mod: Laporan hilang dari layar Moderator

    Note over Admin, DB: Level 2: Eksekutor Tertinggi (Panel Admin)
    Admin->>API: Buka EscalatedReviewResource
    API-->>Admin: Tampilkan Laporan (Filter: status='escalated')

    Admin->>API: Nyalakan Toggle "BAN USER" & Klik "Selesaikan"

    API->>DB: (beforeSave) Tangkap Flag $shouldBanUser
    API->>DB: (afterSave) UPDATE users SET banned_at = NOW()
    API->>DB: (afterSave) UPDATE reviews SET deleted_at = NOW() (SoftDelete)
    API->>DB: UPDATE review_reports SET status = 'resolved', admin_id = Admin.ID

    API-->>Admin: Eksekusi Berhasil. User Terblokir.
```

---

## Fitur Utama

### Eskalasi Bertingkat

Sistem moderasi menggunakan tingkat eskalasi yang jelas:

- **Level 1 - Moderator**: Menyembunyikan komentar atau menghapus ulasan
- **Level 2 - Admin**: Pemblokiran akun permanen dan penghapusan konten massal

### Pelaporan Komunitas

- Pembaca dapat melaporkan komentar, ulasan, atau novel yang melanggar kebijakan
- Setiap laporan masuk ke kolam laporan (pending) untuk ditinjau
- Moderator melakukan investigasi dan memberikan rekomendasi

### RBAC (Role-Based Access Control)

Sistem membatasi akses berdasarkan peran:

- **Moderator**: Review dan kelola konten ringan (sembunyikan/hapus)
- **Admin**: Hanya menangani kasus eskalasi dan pemblokiran akun
- **Editor**: Dapat melaporkan novel yang bermasalah

### Soft Delete & Audit Trail

- Ulasan dan konten dihapus secara soft (tidak dari database)
- Admin dapat melacak riwayat tindakan moderasi
- Setiap keputusan dicatat dengan alasan dan timestamp
