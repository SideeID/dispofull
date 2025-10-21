# Sequence Diagrams — E‑Surat (Mermaid)

Dokumen ini menyajikan diagram urutan (sequence) utama sistem E‑Surat menggunakan Mermaid. Kode dapat dirender langsung di GitHub, VS Code (dengan ekstensi Mermaid), atau alat pendukung Mermaid lainnya.

## Incoming Letter Disposition Workflow

```mermaid
sequenceDiagram
    autonumber
    actor Rektorat
    participant WebApp
    participant LetterService
    participant DispoSvc as DispositionService
    actor "Unit Kerja" as UnitKerja

    Rektorat->>WebApp: Buka surat masuk
    WebApp->>LetterService: Mark received_at
    LetterService-->>WebApp: OK

    Rektorat->>WebApp: Buat disposisi ke Unit Kerja
    WebApp->>DispoSvc: create(letter_id, to_user/to_department)
    DispoSvc-->>WebApp: status = pending

    UnitKerja->>WebApp: Buka daftar disposisi
    WebApp->>DispoSvc: markAsRead(disposition)
    DispoSvc-->>WebApp: read_at set

    UnitKerja->>WebApp: Selesaikan pekerjaan
    WebApp->>DispoSvc: markAsCompleted(disposition, response)
    DispoSvc-->>WebApp: status = completed, completed_at

    WebApp->>LetterService: Evaluasi status surat (processed/archived)
    LetterService-->>WebApp: Status terkini

    note over DispoSvc,LetterService: Tabel terkait: letters, letter_dispositions
```

## Outgoing Letter Creation — Auto Number & Attachments

```mermaid
sequenceDiagram
    autonumber
    actor "Unit Kerja" as UnitKerja
    participant WebApp
    participant NumSvc as NumberingService
    participant FileStore as Storage
    participant LetterService

    UnitKerja->>WebApp: Buat surat baru (isi metadata)
    UnitKerja->>WebApp: Upload lampiran
    WebApp->>FileStore: Simpan lampiran (binary)
    FileStore-->>WebApp: file_path, file_type, file_size

    WebApp->>NumSvc: generateUniqueLetterNumber(letter_type_id, department_id)
    NumSvc-->>WebApp: letter_number (format sesuai LetterType/Sequence)

    WebApp->>LetterService: save(letter, attachments, letter_number)
    LetterService-->>WebApp: OK (status = draft/pending)

    WebApp-->>UnitKerja: Notifikasi: Surat tersimpan

    note over NumSvc,LetterService: Tabel terkait: letters, letter_attachments, letter_number_sequences, letter_types
```

## Catatan

-   Mermaid ditulis dalam blok kode ```mermaid agar dapat dirender otomatis.
-   Nama partisipan diselaraskan dengan terminologi di kode/model: LetterService, DispositionService, NumberingService, Storage.
-   Alur di atas mengacu pada rancangan di `system.puml` dan dokumen desain database.
