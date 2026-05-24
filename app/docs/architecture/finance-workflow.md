💰 Arsitektur Logika Bisnis: Ekosistem Finansial & Dompet Digital
Dokumen ini memetakan perputaran ekonomi di dalam platform Storymoon. Sistem ini menggunakan arsitektur Ledger (Buku Besar) pada setiap transaksi pembelian bab untuk memastikan tidak ada kebocoran perhitungan bagi hasil antara Penulis dan Platform. Sistem juga dilindungi dengan DB::transaction mutlak.

1. Use Case Diagram (Aktor & Hak Akses Finansial)
   Diagram ini menunjukkan interaksi antara Pembaca (sumber dana), Penulis (penerima dana), Sistem (kalkulator otomatis), dan Admin Finance (validator fiat).

Cuplikan kode
flowchart LR
%% Aktor Utama
Pembaca(["📖 Pembaca (Reader)"])
Penulis(["🧑‍💻 Penulis (Author)"])
Finance(["🏦 Admin Finance"])
Sistem(["🤖 Sistem Otomatis"])

    %% Use Cases
    UC1("Top-Up Koin via Payment Gateway")
    UC2("Beli Bab Premium (Chapter Purchase)")
    UC3("Kalkulasi Bagi Hasil 70:30 atau 50:50")
    UC4("Generate Slip Pendapatan Bulanan")
    UC5("Ajukan Penarikan Dana (Withdrawal)")
    UC6("Validasi & Transfer Uang Fiat (Rp)")
    UC7("Refund Koin (Jika Penarikan Gagal)")

    %% Relasi Pembaca
    Pembaca --> UC1
    Pembaca --> UC2

    %% Relasi Penulis
    Penulis --> UC5
    Penulis -. "Menerima" .-> UC4

    %% Relasi Admin Finance
    Finance --> UC6
    Finance --> UC7

    %% Relasi Sistem (Otomatisasi)
    Sistem --> UC3
    Sistem --> UC4
    UC2 -. "Memicu" .-> UC3

2. Activity Diagram (Alur Pembelian Bab & Bagi Hasil Otomatis)
   Diagram aktivitas ini menjelaskan apa yang terjadi di sepersekian detik ketika seorang pembaca menekan tombol "Buka Bab Premium". Sistem secara cerdas akan mengecek "Snapshot Kontrak" untuk menentukan porsi pembagian koin.

Cuplikan kode
stateDiagram-v2
[*] --> Klik_Beli : Pembaca klik Beli Bab Premium

    Klik_Beli --> Cek_Saldo : Sistem mengecek Dompet Pembaca

    state Cek_Saldo <<choice>>
    Cek_Saldo --> Top_Up : Saldo Koin < Harga Bab
    Cek_Saldo --> Potong_Saldo : Saldo Koin >= Harga Bab

    Top_Up --> Cek_Saldo : Pembaca berhasil Top-Up

    Potong_Saldo --> Cek_Kontrak : Koin ditarik dari dompet pembaca

    state Cek_Kontrak <<choice>>
    Cek_Kontrak --> Split_Eksklusif : Kontrak Eksklusif
    Cek_Kontrak --> Split_NonEksklusif : Kontrak Non-Eksklusif

    Split_Eksklusif --> Catat_Ledger : Author 70% | Platform 30%
    Split_NonEksklusif --> Catat_Ledger : Author 50% | Platform 50%

    Catat_Ledger --> Suntik_Dompet : Simpan ke tabel 'chapter_purchases'

    Suntik_Dompet --> Buka_Bab : Tambah revenue ke Dompet Platform & Penulis

    Buka_Bab --> [*] : Pembaca bisa membaca isi cerita

3. Sequence Diagram (Alur Pencairan Dana / Withdrawal)
   Diagram urutan ini menunjukkan tingkat keamanan tinggi (Fraud Prevention) saat penulis ingin mencairkan koinnya menjadi uang Rupiah. Koin akan "dibekukan" terlebih dahulu agar tidak bisa ditarik dua kali.

Cuplikan kode
sequenceDiagram
autonumber
actor Author as Penulis
participant Front as Frontend / Dasbor
participant DB as MySQL (Wallet & Withdrawal)
actor Finance as Admin Finance
participant Bank as Bank Lokal / Midtrans

    Author->>Front: Masukkan nominal koin untuk dicairkan
    Front->>DB: DB::transaction() Dimulai
    DB->>DB: Cek Saldo & Kurangi coin_balance (Bekukan Koin)
    DB->>DB: INSERT into withdrawals (status: pending)
    DB-->>Front: DB::transaction() Selesai (Commit)
    Front-->>Author: Notifikasi "Pengajuan sedang diproses"

    Note over Finance, DB: Fase Audit Keuangan
    Finance->>DB: Buka Panel Finance (WithdrawalResource)
    DB-->>Finance: Tampilkan data pending & Rekening Penulis

    Finance->>Bank: Transfer Manual / API Disbursement (Rupiah)

    alt Transfer Sukses
        Bank-->>Finance: Resi Bukti Transfer
        Finance->>DB: Upload Bukti & Set Status = 'approved'
        DB-->>Author: Notifikasi "Dana berhasil cair!"
    else Transfer Gagal (Rekening Salah/Pasif)
        Finance->>DB: Set Status = 'rejected' & Tulis Alasan
        DB->>DB: DB::transaction() Dimulai
        DB->>DB: Kembalikan Koin ke Wallet Penulis (Refund)
        DB-->>Front: DB::transaction() Selesai (Commit)
        DB-->>Author: Notifikasi "Penarikan Gagal. Koin dikembalikan."
    end
