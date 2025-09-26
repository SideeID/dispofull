# Database Design - Sistem Pengelolaan Surat Masuk & Keluar Universitas Bakrie

## Overview

Database ini dirancang untuk mendukung Sistem Pengelolaan Surat Masuk & Keluar Universitas Bakrie dengan fitur utama:

-   Tracking Surat (monitor status surat masuk & keluar)
-   Penyimpanan Surat (arsip digital)
-   Tanda Tangan Elektronik
-   Pembuatan Surat Otomatis (nomor surat otomatis)
-   Pembuatan Agenda Surat (via web, output PDF)

## Database Schema

### 1. Tabel `users`

Menyimpan data pengguna sistem dengan 3 role: admin, rektorat, unit_kerja.

**Kolom utama:**

-   `nip`: Nomor Induk Pegawai
-   `position`: Jabatan
-   `role`: admin | rektorat | unit_kerja
-   `department_id`: Relasi ke departemen
-   `signature_path`: Path tanda tangan digital

### 2. Tabel `departments`

Menyimpan data departemen/unit kerja di universitas.

**Kolom utama:**

-   `name`: Nama departemen
-   `code`: Kode departemen (untuk nomor surat)
-   `type`: rektorat | unit_kerja
-   `is_active`: Status aktif

**Data default:** Rektorat, WR1, WR2, WR3, BAA, Fakultas, Program Studi, P3M, BTI

### 3. Tabel `letter_types`

Menyimpan jenis-jenis surat yang dapat dibuat.

**Kolom utama:**

-   `name`: Nama jenis surat
-   `code`: Kode untuk nomor surat
-   `number_format`: Format nomor surat (template)

**Data default:** SK, ST, SE, SU, SP, SKet, SR, ND, BA, SPj, SM

### 4. Tabel `letters`

Tabel utama untuk menyimpan data surat.

**Kolom utama:**

-   `letter_number`: Nomor surat (auto-generated)
-   `subject`: Perihal surat
-   `content`: Isi surat
-   `direction`: incoming | outgoing
-   `status`: draft | pending | processed | archived | rejected
-   `priority`: low | normal | high | urgent
-   `letter_type_id`: Relasi ke jenis surat
-   `from_department_id` / `to_department_id`: Departemen pengirim/penerima
-   `original_file_path` / `signed_file_path`: Path file surat

### 5. Tabel `letter_dispositions`

Menyimpan data disposisi surat (instruksi tindak lanjut).

**Kolom utama:**

-   `letter_id`: Relasi ke surat
-   `from_user_id` / `to_user_id`: User yang memberi/menerima disposisi
-   `instruction`: Instruksi disposisi
-   `due_date`: Batas waktu penyelesaian
-   `status`: pending | in_progress | completed | returned

### 6. Tabel `letter_attachments`

Menyimpan lampiran surat.

**Kolom utama:**

-   `letter_id`: Relasi ke surat
-   `original_name` / `file_name`: Nama file asli/disimpan
-   `file_path`: Path file
-   `file_size`: Ukuran file

### 7. Tabel `letter_number_sequences`

Mengelola sequence nomor surat otomatis per jenis surat dan departemen.

**Kolom utama:**

-   `letter_type_id`: Jenis surat
-   `department_id`: Departemen
-   `year`: Tahun
-   `last_number`: Nomor terakhir yang digunakan

### 8. Tabel `letter_signatures`

Menyimpan data tanda tangan elektronik.

**Kolom utama:**

-   `letter_id`: Relasi ke surat
-   `user_id`: User yang menandatangani
-   `signature_type`: digital | electronic
-   `signature_data`: Data tanda tangan (base64)
-   `signed_at`: Waktu penandatanganan

### 9. Tabel `letter_agendas`

Menyimpan data agenda surat untuk laporan.

**Kolom utama:**

-   `title`: Judul agenda
-   `start_date` / `end_date`: Periode surat
-   `type`: daily | weekly | monthly
-   `filters`: Kriteria filter (JSON)
-   `pdf_path`: Path file PDF agenda

## Models dan Relationships

### Model Relationships:

-   `User` belongsTo `Department`
-   `Department` hasMany `Users`, `Letters`, `LetterAgendas`
-   `Letter` belongsTo `LetterType`, `User`, `Department`
-   `Letter` hasMany `LetterDispositions`, `LetterAttachments`, `LetterSignatures`
-   `LetterType` hasMany `Letters`, `LetterNumberSequences`

### Key Methods:

-   `LetterNumberSequence::generateLetterNumber()`: Generate nomor surat otomatis
-   `Letter::isSigned()`: Cek status tanda tangan
-   `LetterDisposition::isOverdue()`: Cek disposisi terlambat
-   `LetterAgenda::getFilteredLetters()`: Ambil surat sesuai filter agenda

## User Roles & Permissions

### 1. Admin (BTI)

-   Mengelola sistem & pemeliharaan aplikasi
-   Monitoring performa aplikasi
-   Pengaturan akses dan role pengguna

### 2. Rektorat

-   Dashboard
-   Surat Masuk
-   Surat Tugas
-   History Disposisi
-   Inbox Surat Tugas
-   Membuat/menindaklanjuti surat tugas
-   Arsip Surat Tugas

### 3. Unit Kerja (BAA, Staff Prodi, dll)

-   Dashboard
-   Surat Masuk
-   Buat Surat
-   Arsip Surat Tugas

## Seeder Data

Database sudah dilengkapi dengan data awal:

-   13 Departemen (Rektorat + Unit Kerja)
-   11 Jenis Surat
-   4 User sample dengan role berbeda

## Migration Files

1. `2014_10_12_000000_create_users_table.php`
2. `2025_09_26_031335_create_departments_table.php`
3. `2025_09_26_031343_create_letter_types_table.php`
4. `2025_09_26_031405_create_letters_table.php`
5. `2025_09_26_031412_create_letter_dispositions_table.php`
6. `2025_09_26_031418_create_letter_attachments_table.php`
7. `2025_09_26_031424_create_letter_number_sequences_table.php`
8. `2025_09_26_031431_create_letter_signatures_table.php`
9. `2025_09_26_031436_create_letter_agendas_table.php`
10. `2025_09_26_032012_add_foreign_keys_to_users_table.php`

## Usage

Untuk menggunakan database ini:

1. Jalankan migrasi:

```bash
php artisan migrate:fresh --seed
```

2. Login dengan user sample:

-   Admin: admin@bakrie.ac.id / password: 123
-   Rektor: rektor@bakrie.ac.id / password: 123
-   Kepala BAA: baa@bakrie.ac.id / password: 123
-   Staff Prodi: ti@bakrie.ac.id / password: 123

3. Gunakan model relationships untuk query data:

```php
// Ambil surat dengan disposisi
$letters = Letter::with(['dispositions', 'attachments', 'signatures'])->get();

// Generate nomor surat otomatis
$sequence = LetterNumberSequence::findOrCreate($letterTypeId, $departmentId);
$letterNumber = $sequence->generateLetterNumber();
```
