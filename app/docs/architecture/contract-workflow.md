# Arsitektur Alur Kerja Kontrak

## Ringkasan

Dokumen ini menguraikan arsitektur logika bisnis untuk sistem pengajuan dan validasi kontrak. Memetakan alur kerja dari:

- **Penulis** memenuhi persyaratan minimum naskah
- **Editor** proses kurasi (sistem klaim naskah)
- **Admin Keuangan** verifikasi legal (KYC - Know Your Customer)

## Daftar Isi

1. [Diagram Use Case](#diagram-use-case-aktor--hak-akses)
2. [Diagram Aktivitas](#diagram-aktivitas-alur-kerja-progresif)
3. [Diagram Urutan](#diagram-urutan-interaksi-sistem--database)

---

## Diagram Use Case (Aktor & Hak Akses)

Diagram ini menunjukkan semua tindakan yang mungkin dilakukan oleh setiap aktor dalam subsistem Kontrak:

```mermaid
flowchart LR
    %% Aktor Utama
    Penulis(["🧑‍💻 Penulis"])
    Editor(["👓 Editor"])
    Admin(["⚖️ Admin (Legal/Keuangan)"])
    Sistem(["🤖 Sistem Storymoon"])

    %% Use Cases
    UC1("Capai Milestone (10 Bab/10k Kata)")
    UC2("Ajukan Kontrak (Eksklusif/Non-Eksklusif)")
    UC3("Klaim Naskah dari Kolam Terbuka")
    UC4("Evaluasi Teks & Naskah")
    UC5("Isi Formulir Legal (KTP & Rekening)")
    UC6("Validasi Keaslian Dokumen KYC")
    UC7("Tanda Tangan Digital (E-Sign)")
    UC8("Aktivasi Kontrak (Monetisasi)")

    %% Relasi Penulis
    Penulis --> UC2
    Penulis --> UC5
    Penulis --> UC7

    %% Relasi Editor
    Editor --> UC3
    Editor --> UC4

    %% Relasi Admin
    Admin --> UC6

    %% Relasi Sistem (Otomasi)
    Sistem --> UC1
    Sistem --> UC8
```

---

## Diagram Aktivitas (Alur Kerja Progresif)

Diagram aktivitas ini mewakili inti dari sistem Progressive Onboarding kami. Perhatikan bagaimana jalur Non-Eksklusif mengambil "jalan pintas", sementara jalur Eksklusif harus melewati verifikasi KYC Admin:

```mermaid
stateDiagram-v2
    [*] --> Tulis_Naskah : Penulis menerbitkan bab

    state Cek_Milestone <<choice>>
    Tulis_Naskah --> Cek_Milestone : Sistem memeriksa kriteria
    Cek_Milestone --> Tulis_Naskah : Persyaratan tidak terpenuhi
    Cek_Milestone --> Ajukan_Kontrak : Persyaratan terpenuhi (10 Bab & 10k Kata)

    Ajukan_Kontrak --> text_review : Penulis memilih jenis kontrak

    state "Kolam Terbuka (Kolam Naskah)" as text_review
    text_review --> Klaim_Naskah : Editor melihat naskah tersedia

    Klaim_Naskah --> Evaluasi_Naskah : Editor A mengklaim (Editor B tidak bisa lihat)

    state Keputusan_Editor <<choice>>
    Evaluasi_Naskah --> Keputusan_Editor : Editor membuat keputusan

    Keputusan_Editor --> [*] : Ditolak
    Keputusan_Editor --> Ajukan_Kontrak : Perlu Revisi
    Keputusan_Editor --> Cek_Tipe_Kontrak : Disetujui

    state Cek_Tipe_Kontrak <<choice>>
    Cek_Tipe_Kontrak --> active : Non-Eksklusif
    Cek_Tipe_Kontrak --> kyc_submission : Eksklusif

    kyc_submission --> kyc_review : Penulis melengkapi KTP & Rekening

    state Keputusan_Admin <<choice>>
    kyc_review --> Keputusan_Admin : Admin memvalidasi dokumen

    Keputusan_Admin --> kyc_submission : KTP Tidak Valid/Buram (Kembali ke Penulis)
    Keputusan_Admin --> signing : Verifikasi Lolos

    signing --> active : Penulis Tanda Tangan dengan E-Sign

    active --> [*] : Kontrak Berjalan & Fitur Koin Aktif!
```

---

## Diagram Urutan (Interaksi Sistem & Database)

Diagram urutan ini menunjukkan bagaimana Frontend, Backend (Laravel), dan Database berinteraksi ketika penulis mengajukan kontrak Eksklusif hingga persetujuan:

```mermaid
sequenceDiagram
    autonumber
    actor Author as Penulis
    participant Front as Frontend (Web/App)
    participant API as Laravel Backend
    participant DB as MySQL Database
    actor Editor as Editor
    actor Admin as Admin Legal

    Author->>Front: Klik "Ajukan Kontrak Eksklusif"
    Front->>API: POST /api/contracts {type: exclusive}
    API->>DB: Validasi total bab & jumlah kata
    API->>DB: INSERT contracts (status: text_review)
    API-->>Front: Response Sukses

    Note over Editor, API: Fase Kurasi Teks
    Editor->>API: GET /admin/text-reviews (Lihat kolam naskah)
    API-->>Editor: Tampilkan naskah tanpa editor_id
    Editor->>API: Klik "Klaim Naskah"
    API->>DB: UPDATE contracts SET editor_id = Editor.ID
    Editor->>API: Klik "Setujui Naskah"
    API->>DB: Logika Progresif: Ubah status ke 'kyc_submission'
    API-->>Editor: Sukses (Naskah hilang dari panel Editor)

    Note over Author, API: Fase Progresif (KYC)
    API-->>Author: Notifikasi: "Naskah Disetujui, Silakan Isi KYC"
    Author->>Front: Upload KTP, Rekening Bank
    Front->>API: POST /api/contracts/kyc
    API->>DB: UPDATE contracts (Upload gambar, status: kyc_review)

    Note over Admin, DB: Fase Legal & Keuangan
    Admin->>API: Buka LegalContractResource
    Admin->>API: Validasi Data (Klik Setujui)
    API->>DB: UPDATE contracts SET status = 'signing'

    Author->>Front: Tanda Tangan Digital & OTP
    Front->>API: POST /api/contracts/sign
    API->>DB: UPDATE contracts SET status = 'active', signed_at = NOW()
    API->>DB: UPDATE novels SET editor_id = Editor.ID (Ikat Supervisor)
    API-->>Author: Selamat! Kontrak Berjalan.
```

---

## Fitur Utama

### Progressive Onboarding

Alur kerja secara progresif meminta informasi dari penulis berdasarkan jenis kontrak:

- **Non-Eksklusif**: Jalur cepat tanpa persyaratan KYC
- **Eksklusif**: Verifikasi KYC lengkap oleh Admin Keuangan

### Sistem Klaim Naskah

- Editor melihat naskah yang tersedia di kolam terbuka
- Hanya satu editor yang dapat mengklaim naskah dalam satu waktu
- Naskah yang diklaim hilang dari kolam untuk editor lain

### Deteksi Milestone Otomatis

- Sistem secara otomatis mendeteksi ketika penulis memenuhi persyaratan (10 bab, 10k kata)
- Mengaktifkan pengajuan kontrak setelah kriteria terpenuhi

### Aktivasi Kontrak

- Setelah persetujuan akhir, kontrak menjadi aktif
- Editor terikat dengan novel tersebut
- Fitur koin dan monetisasi menjadi tersedia
