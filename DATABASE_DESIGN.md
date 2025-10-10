# Database Design — E‑Surat Universitas Bakrie

Dokumen ini merangkum rancangan database berdasarkan seluruh migration dan model Eloquent yang ada di repo ini. Mencakup ERD, deskripsi tabel, relasi, enum/state, indexing, serta catatan implementasi untuk fitur utama: tracking surat, arsip digital, tanda tangan elektronik/digital, penomoran otomatis, dan agenda PDF.

## ERD

```mermaid
erDiagram
  USERS {
    int id
    string name
    string username
    string email
    datetime email_verified_at
    string password
    string nip
    string phone
    string position
    string role
    string status
    int department_id
    string profile_photo_path
    string signature_path
    string two_factor_secret
    string two_factor_recovery_codes
    datetime two_factor_confirmed_at
  }

  DEPARTMENTS {
    int id
    string name
    string code
    string description
    string type
    boolean is_active
  }

  LETTER_TYPES {
    int id
    string name
    string code
    string description
    string number_format
    boolean is_active
  }

  LETTERS {
    int id
    string letter_number
    string subject
    string content
    date letter_date
    string direction
    string status
    string priority
    string sender_name
    string sender_address
    string recipient_name
    string recipient_address
    int letter_type_id
    int created_by
    int from_department_id
    int to_department_id
    string original_file_path
    string signed_file_path
    datetime received_at
    datetime processed_at
    datetime archived_at
    string notes
  }

  LETTER_DISPOSITIONS {
    int id
    int letter_id
    int from_user_id
    int to_user_id
    int to_department_id
    string instruction
    string priority
    date due_date
    string status
    string response
    datetime read_at
    datetime completed_at
  }

  LETTER_ATTACHMENTS {
    int id
    int letter_id
    string original_name
    string file_name
    string file_path
    string file_type
    int file_size
    string description
    int uploaded_by
  }

  LETTER_SIGNATURES {
    int id
    int letter_id
    int user_id
    string signature_type
    string signature_path
    string signature_data
    datetime signed_at
    string ip_address
    string user_agent
    string status
    string notes
  }

  LETTER_NUMBER_SEQUENCES {
    int id
    int letter_type_id
    int department_id
    int year
    int last_number
    string prefix
    string suffix
  }

  LETTER_AGENDAS {
    int id
    string title
    string description
    date agenda_date
    date start_date
    date end_date
    string type
    int department_id
    int created_by
    string status
    string pdf_path
    string filters
  }

  %% Relationships
  DEPARTMENTS ||--o{ USERS : department_id
  LETTER_TYPES ||--o{ LETTERS : letter_type_id
  USERS ||--o{ LETTERS : created_by
  DEPARTMENTS ||--o{ LETTERS : from_department_id
  DEPARTMENTS ||--o{ LETTERS : to_department_id

  LETTERS ||--o{ LETTER_DISPOSITIONS : letter_id
  USERS ||--o{ LETTER_DISPOSITIONS : from_user_id
  USERS ||--o{ LETTER_DISPOSITIONS : to_user_id
  DEPARTMENTS ||--o{ LETTER_DISPOSITIONS : to_department_id

  LETTERS ||--o{ LETTER_ATTACHMENTS : letter_id
  USERS ||--o{ LETTER_ATTACHMENTS : uploaded_by

  LETTERS ||--o{ LETTER_SIGNATURES : letter_id
  USERS ||--o{ LETTER_SIGNATURES : user_id

  LETTER_TYPES ||--o{ LETTER_NUMBER_SEQUENCES : letter_type_id
  DEPARTMENTS ||--o{ LETTER_NUMBER_SEQUENCES : department_id

  DEPARTMENTS ||--o{ LETTER_AGENDAS : department_id
  USERS ||--o{ LETTER_AGENDAS : created_by
```

---

## Ringkasan Sistem

E‑Surat mengelola surat masuk/keluar, disposisi, lampiran, penomoran otomatis, tanda tangan, dan agenda PDF. Role: admin, rektorat, unit_kerja. Dokumen ini memetakan tabel, relasi, state machine, serta alur data untuk fitur:

-   Tracking surat (masuk & keluar)
-   Arsip digital
-   Tanda tangan elektronik/digital (opsional)
-   Penomoran otomatis
-   Agenda surat (web → PDF)

---

## Deskripsi Entitas dan Tabel

### users

Identitas dan otorisasi pengguna.

Kolom utama:

-   id (PK)
-   name, username (unique), email (unique), password
-   email_verified_at (nullable)
-   nip (unique, nullable), phone (nullable), position (nullable)
-   role enum: admin|rektorat|unit_kerja (default unit_kerja)
-   status enum: active|inactive (default active)
-   department_id (FK → departments.id, nullable, ON DELETE SET NULL)
-   profile_photo_path (nullable), signature_path (nullable)
-   two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at (opsional via Fortify)
-   timestamps

Relasi Eloquent:

-   belongsTo department
-   hasMany: createdLetters, sentDispositions, receivedDispositions, uploadedAttachments, letterSignatures, createdAgendas

Index/Constraint: username unique, email unique, department_id FK

### departments

Master unit kerja/rektorat.

Kolom:

-   id (PK), name, code (unique), description (nullable)
-   type enum: rektorat|unit_kerja (default unit_kerja)
-   is_active boolean (default true)
-   timestamps

Relasi:

-   hasMany: users, lettersFrom (from_department_id), lettersTo (to_department_id), letterNumberSequences, letterAgendas, letterDispositions (to_department_id)

### letter_types

Master jenis surat.

Kolom:

-   id (PK), name, code (unique), description (nullable)
-   number_format (nullable)
-   is_active boolean (default true)
-   timestamps

Relasi: hasMany letters, letterNumberSequences

### letters

Entitas surat (masuk/keluar) termasuk konten, status, dan tracking.

Kolom:

-   id (PK)
-   letter_number (unique)
-   subject, content (nullable)
-   letter_date (date)
-   direction enum: incoming|outgoing
-   status enum: draft|pending|processed|archived|rejected (default draft)
-   priority enum: low|normal|high|urgent (default normal)
-   sender_name/address (nullable), recipient_name/address (nullable)
-   letter_type_id (FK → letter_types.id, RESTRICT)
-   created_by (FK → users.id, RESTRICT)
-   from_department_id, to_department_id (FK → departments.id, nullable, ON DELETE SET NULL)
-   original_file_path, signed_file_path (nullable)
-   received_at, processed_at, archived_at (nullable)
-   notes (nullable), timestamps

Index: (direction,status), letter_date, created_by

Relasi: belongsTo letterType, creator, fromDepartment, toDepartment; hasMany dispositions, attachments, signatures

### letter_dispositions

Pencatatan disposisi surat antar user/departemen.

Kolom:

-   id (PK)
-   letter_id (FK → letters.id, CASCADE)
-   from_user_id, to_user_id (FK → users.id, RESTRICT)
-   to_department_id (FK → departments.id, nullable, SET NULL)
-   instruction (text), priority enum, due_date (nullable)
-   status enum: pending|in_progress|completed|returned (default pending)
-   response (nullable), read_at (nullable), completed_at (nullable)
-   timestamps

Index: letter_id, (to_user_id,status), due_date

Relasi: belongsTo letter, fromUser, toUser, toDepartment

### letter_attachments

Lampiran file untuk surat.

Kolom:

-   id (PK)
-   letter_id (FK → letters.id, CASCADE)
-   original_name, file_name, file_path, file_type, file_size
-   description (nullable)
-   uploaded_by (FK → users.id, RESTRICT)
-   timestamps

Index: letter_id

Relasi: belongsTo letter, uploader

### letter_signatures

Tanda tangan surat (digital/electronic).

Kolom:

-   id (PK)
-   letter_id (FK → letters.id, CASCADE)
-   user_id (FK → users.id, RESTRICT)
-   signature_type enum: digital|electronic (default digital)
-   signature_path (nullable), signature_data (nullable)
-   signed_at (nullable), ip_address (nullable), user_agent (nullable)
-   status enum: pending|signed|rejected (default pending)
-   notes (nullable), timestamps

Index: letter_id, user_id

Relasi: belongsTo letter, user

### letter_number_sequences

Penomoran otomatis per kombinasi (jenis surat, departemen opsional, tahun).

Kolom:

-   id (PK)
-   letter_type_id (FK → letter_types.id, CASCADE)
-   department_id (FK → departments.id, CASCADE, nullable)
-   year (YEAR)
-   last_number (uint, default 0)
-   prefix, suffix (nullable)
-   timestamps

Unique: (letter_type_id, department_id, year)

Relasi: belongsTo letterType, department

Perilaku (model): getNextNumber(), generateLetterNumber(), generateUniqueLetterNumber(), findOrCreate()

### letter_agendas

Agenda surat (periode, filter, output PDF).

Kolom:

-   id (PK)
-   title, description (nullable)
-   agenda_date, start_date, end_date
-   type enum: daily|weekly|monthly (default monthly)
-   department_id (FK → departments.id, SET NULL, nullable)
-   created_by (FK → users.id, RESTRICT)
-   status enum: draft|published|archived (default draft)
-   pdf_path (nullable)
-   filters (json, nullable)
-   timestamps

Index: agenda_date, department_id, status

Relasi: belongsTo department, creator

### Tabel Pendukung

-   sessions: penyimpanan sesi Laravel
-   personal_access_tokens: token API (Sanctum)
-   failed_jobs: log kegagalan queue

---

## State Machine (Enum/Status)

-   users.role: admin | rektorat | unit_kerja
-   users.status: active | inactive
-   departments.type: rektorat | unit_kerja
-   letters.direction: incoming | outgoing
-   letters.status: draft → pending → processed → archived; atau pending → rejected
-   letters.priority: low | normal | high | urgent
-   letter_dispositions.status: pending → in_progress → completed; atau returned
-   letter_dispositions.priority: low | normal | high | urgent
-   letter_signatures.signature_type: digital | electronic
-   letter_signatures.status: pending | signed | rejected
-   letter_agendas.type: daily | weekly | monthly
-   letter_agendas.status: draft | published | archived

Pastikan logika aplikasi menegakkan transisi yang valid dan sinkron dengan proses bisnis.

---

## Penomoran Surat

Sumber data: `letter_number_sequences` digabung `letter_types.number_format`.

-   Dimensi: `letter_type_id`, `department_id` (opsional), `year`
-   `last_number` bertambah saat generate
-   Placeholder format yang didukung model:
    -   {number} — nomor ber-padding (default 3 digit)
    -   {code} — kode jenis surat (`letter_types.code`)
    -   {department_code} — kode departemen (jika sequence terkait departemen)
    -   {month} — 01..12
    -   {month_roman} — I..XII
    -   {year} — YYYY
    -   {prefix} / {suffix} — dari sequence

Fallback format default (di model):
`{number}/UB/R-{code}/{month_roman}/{year}`

Contoh yang umum dipakai kampus:
`{number}/UB/{department_code}/{code}/{month_roman}/{year}`

---

## Mapping Fitur ↔ Tabel

-   Tracking Surat: `letters` (status, timestamps) + `letter_dispositions` + `letter_signatures`
-   Arsip Digital: `letters.original_file_path`/`signed_file_path`, `letter_attachments`
-   Tanda Tangan: `letter_signatures` (status, signed_at, ip, user_agent, path/data)
-   Penomoran: `letter_number_sequences` + `letter_types.number_format` + `letters.letter_number`
-   Agenda PDF: `letter_agendas.filters` → query `letters` → simpan ke `pdf_path`

---

## Indeks & Kinerja

-   letters: (direction, status), letter_date, created_by
-   letter_dispositions: letter_id, (to_user_id, status), due_date
-   letter_attachments: letter_id
-   letter_signatures: letter_id, user_id
-   letter_agendas: agenda_date, department_id, status
-   Unique constraints: users.username, users.email, departments.code, letter_types.code, letters.letter_number, letter_number_sequences (type, department, year)

---

## Seeder Jenis Surat (usulan kode)

Silakan sesuaikan di `database/seeders/LetterTypeSeeder.php`.

1. Surat Keluar → OUT
2. Surat Edaran → SE
3. Surat Tugas → ST
4. Surat Pengumuman → PENG
5. Internal Memo → IM
6. Surat Ijin Atasan → SIA
7. Surat Perintah → SPR
8. Surat Keterangan → SKET
9. Surat Rekomendasi → SR
10. Surat Pernyataan → SPN
11. Surat Peringatan → SP
12. Surat Pernyataan Tanggung Jawab Mutlak → SPTJM
13. Surat Kuasa → SKU
14. Pakta Integritas → PI
15. Surat Perintah Kerja → SPK
16. Surat Keputusan Rektor → SKR
17. Peraturan Rektor → PR
18. Peraturan Universitas → PU
19. MOU → MOU
20. PKS → PKS

Rekomendasi number_format default: `{number}/UB/{department_code}/{code}/{month_roman}/{year}`

---

## Catatan Desain & Rekomendasi

-   Draft tanpa nomor: saat ini `letters.letter_number` adalah NOT NULL dan UNIQUE. Jika ingin membuat draft tanpa nomor, pertimbangkan menjadikannya nullable dan mengisi saat submit; atau gunakan placeholder unik (UUID) lalu ganti saat submit.
-   Konsistensi path file: `original_file_path` (file awal), `signed_file_path` (final), lampiran di `letter_attachments.file_path`.
-   `letter_number_sequences.department_id` nullable memungkinkan skema penomoran global atau per-departemen.
-   Terapkan policy/authorization per role untuk query sensitif (misal batasan akses per departemen bagi unit_kerja).

---

## Versi dan Sumber

Sesuai migrasi di `database/migrations` dan model di `app/Models` pada branch saat ini. Jika skema berubah, perbarui ERD dan bagian yang relevan di dokumen ini.

---

## UML Diagrams

Di bawah ini adalah kumpulan diagram UML yang menggambarkan sistem E‑Surat dari sisi use case, class, sequence (alur utama), dan state untuk entitas Surat. GitHub tidak merender PlantUML secara native; gunakan VS Code + ekstensi PlantUML untuk melihat blok PlantUML atau jadikan sebagai referensi desain. Mermaid dirender otomatis di GitHub dan VS Code.

### Use Case Diagram (PlantUML)

```plantuml
@startuml
left to right direction
actor Admin
actor Rektorat
actor "Unit Kerja" as UnitKerja

rectangle "E‑Surat Universitas Bakrie" {
  (Monitoring Aplikasi) as UC_Monitor
  (Kelola Pengguna) as UC_Users
  (Kelola Departemen) as UC_Dept
  (Kelola Jenis Surat) as UC_Types

  (Dashboard) as UC_Dash
  (Surat Masuk) as UC_Incoming
  (Surat Tugas) as UC_Task
  (Inbox Surat Tugas) as UC_TaskInbox
  (History Disposisi) as UC_History
  (Tindak Lanjut Surat Tugas) as UC_TaskFollow
  (Arsip Surat Tugas) as UC_TaskArchive

  (Buat Surat) as UC_CreateLetter
  (Upload Lampiran) as UC_Attach
  (Nomor Surat Otomatis) as UC_Numbering
  (Tanda Tangan Surat) as UC_Sign
  (Agenda Surat PDF) as UC_Agenda
}

Admin --> UC_Monitor
Admin --> UC_Users
Admin --> UC_Dept
Admin --> UC_Types

Rektorat --> UC_Dash
Rektorat --> UC_Incoming
Rektorat --> UC_Task
Rektorat --> UC_TaskInbox
Rektorat --> UC_History
Rektorat --> UC_TaskFollow
Rektorat --> UC_TaskArchive
Rektorat --> UC_Sign

UnitKerja --> UC_Dash
UnitKerja --> UC_CreateLetter
UnitKerja --> UC_Attach
UnitKerja --> UC_Numbering
UnitKerja --> UC_Sign
UnitKerja --> UC_TaskArchive
UnitKerja --> UC_Incoming
UnitKerja --> UC_Agenda

UC_CreateLetter .> UC_Attach : include
UC_CreateLetter .> UC_Numbering : include
UC_Numbering .> UC_Types : <<uses master>>
UC_Sign .> UC_CreateLetter : <<after submit>>
UC_Agenda .> UC_Incoming : <<data source>>
@enduml
```

Jika Anda membutuhkan versi Mermaid, use case dapat didekati dengan flowchart nodes (namun tidak se‑standar PlantUML use case).

### Class Diagram (Mermaid)

```mermaid
classDiagram
  class User {
    +int id
    +string name
    +string username
    +string email
    +string role
    +string status
    +int department_id
  }
  class Department {
    +int id
    +string name
    +string code
    +string type
    +bool is_active
  }
  class LetterType {
    +int id
    +string name
    +string code
    +string number_format
  }
  class Letter {
    +int id
    +string letter_number
    +date letter_date
    +string direction
    +string status
    +int letter_type_id
    +int created_by
    +int from_department_id
    +int to_department_id
  }
  class LetterDisposition {
    +int id
    +int letter_id
    +int from_user_id
    +int to_user_id
    +int to_department_id
    +string status
  }
  class LetterAttachment {
    +int id
    +int letter_id
    +string file_name
    +string file_path
    +int uploaded_by
  }
  class LetterSignature {
    +int id
    +int letter_id
    +int user_id
    +string signature_type
    +string status
  }
  class LetterNumberSequence {
    +int id
    +int letter_type_id
    +int department_id
    +int year
    +int last_number
  }
  class LetterAgenda {
    +int id
    +string title
    +date start_date
    +date end_date
    +string type
    +int department_id
    +int created_by
    +string status
  }

  Department --> User : department_id
  LetterType --> Letter : letter_type_id
  User --> Letter : created_by
  Department --> Letter : from_department_id
  Department --> Letter : to_department_id
  Letter --> LetterDisposition : 1..*
  User --> LetterDisposition : from_user_id
  User --> LetterDisposition : to_user_id
  Department --> LetterDisposition : to_department_id
  Letter --> LetterAttachment : 1..*
  User --> LetterAttachment : uploaded_by
  Letter --> LetterSignature : 1..*
  User --> LetterSignature : user_id
  LetterType --> LetterNumberSequence : 1..*
  Department --> LetterNumberSequence : 0..*
  Department --> LetterAgenda : 0..*
  User --> LetterAgenda : created_by
```

### Sequence Diagram — Buat & Submit Surat (Unit Kerja)

```mermaid
sequenceDiagram
  autonumber
  actor Staff as Unit Kerja
  participant UI as Web UI
  participant UKC as UnitKerjaController
  participant L as Letter
  participant LA as LetterAttachment
  participant LNS as LetterNumberSequence
  participant LS as LetterSignature

  Staff->>UI: Isi form Buat Surat + lampiran
  UI->>UKC: POST /unit-kerja/api/letters/draft
  UKC->>L: create(draft)
  UI->>UKC: POST /unit-kerja/api/letters/{id}/attachments
  UKC->>LA: create(attachment)
  UI->>UKC: POST /unit-kerja/api/letters/submit
  UKC->>LNS: findOrCreate(letter_type, department, year)
  LNS-->>UKC: sequence instance
  UKC->>LNS: generateUniqueLetterNumber()
  LNS-->>UKC: nomor surat
  UKC->>L: update(letter_number,status=pending)
  UKC->>LS: create(pending signers)
  UKC-->>UI: 200 OK + nomor surat
```

### Sequence Diagram — Disposisi Surat Masuk (Rektorat)

```mermaid
sequenceDiagram
  autonumber
  actor Rektor
  participant RC as RektoratController
  participant L as Letter
  participant LD as LetterDisposition

  Rektor->>RC: Lihat Surat Masuk
  RC->>L: incomingIndex()
  L-->>RC: daftar surat
  Rektor->>RC: Buat Disposisi (instruksi, due_date, penerima)
  RC->>LD: create(letter_id, from_user_id, to_user_id, to_department_id, ...)
  LD-->>RC: status=pending
  RC-->>Rektor: 200 OK
```

### State Diagram — Lifecycle Surat

```mermaid
stateDiagram-v2
  [*] --> Draft
  Draft --> Pending: submit
  Pending --> Processed: process
  Pending --> Rejected: reject
  Processed --> Archived: archive
  Pending --> Archived: archive
  Draft --> [*]: cancel (opsional)
```
