<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="{
        loading: false,
        loadingLetterTypes: false,
        loadingSubmit: false,
        changed: false,
        error: null,
        flash: null,
        draftId: null,
        letterTypes: @js($letterTypes ?? []),
        userDepartmentId: @js($userDeptId ?? null),
        availableDepartments: [],
        signers: [],
        signerSearch: '',
        numberingPreview: null,
        attachments: [],
        participants: [],
        signer: null,
        internalTemp: '',
        externalTemp: '',
        debounceTimers: {},
        participantSearch: '',
        manual: { nama: '', nip: '', jabatan: '' },
        form: { letter_type_id: '', jenis: '', perihal: '', tanggal: (new Date()).toISOString().slice(0, 10), prioritas: 'normal', klasifikasi: 'biasa', ringkasan: '', konten: '', catatanInternal: '', tujuanInternal: [], tujuanExternal: [], nomor: { prefix: '', seq: null, unit: '', tahun: (new Date()).getFullYear(), suffix: '' } },
        openFlags: { showPreview: false, showParticipants: false, showAttachments: false, showTemplates: false, showSigner: false, showNumbering: false, showSubmitConfirm: false },
        csrf() { const el = document.querySelector('meta[name=csrf-token]'); return el ? el.content : ''; },
        notify(msg, type = 'flash') {
            if (type === 'flash') this.flash = msg;
            else this.error = msg;
            setTimeout(() => { if (this.flash === msg) this.flash = null; }, 4000);
        },
        debounce(key, fn, delay = 400) {
            if (this.debounceTimers[key]) clearTimeout(this.debounceTimers[key]);
            this.debounceTimers[key] = setTimeout(fn, delay);
        },
        open(flag) {
            this.closeAll();
            this.openFlags[flag] = true;
        },
        closeAll() { Object.keys(this.openFlags).forEach(k => this.openFlags[k] = false); },
        markChanged() { this.changed = true; },
        contentLength() { return (this.form.konten || '').length; },
        updateContent() { this.markChanged(); },
        async loadLetterTypes() {
            this.loadingLetterTypes = true;
            this.error = null;
            try {
                const r = await fetch('/unit-kerja/api/letter-types');
                const j = await r.json();
                if (!j.success) throw new Error(j.message || 'Gagal memuat jenis surat');
                this.letterTypes = j.data || [];
                if (!this.form.letter_type_id && this.letterTypes.length) {
                    this.form.letter_type_id = this.letterTypes[0].id;
                    this.onLetterTypeChange();
                }
            } catch (e) { this.error = e.message; } finally { this.loadingLetterTypes = false; }
        },
        async fetchSigners(q = '') {
            try {
                const r = await fetch(`/unit-kerja/api/penandatangan?q=${encodeURIComponent(q||this.signerSearch)}`);
                const j = await r.json();
                this.signers = j.success ? j.data : [];
            } catch (e) {}
        },
        async fetchDepartments() {
            try {
                const r = await fetch('/unit-kerja/api/departments');
                const j = await r.json();
                if (j.success) this.availableDepartments = j.data || [];
            } catch (e) { console.error('Failed to load departments:', e); }
        },
        searchSigners() { this.debounce('signerSearch', () => this.fetchSigners(this.signerSearch), 400); },
        addInternal() {
            if (this.internalTemp) {
                this.form.tujuanInternal.push(this.internalTemp);
                this.internalTemp = '';
                this.markChanged();
            }
        },
        removeInternal(v) {
            this.form.tujuanInternal = this.form.tujuanInternal.filter(x => x !== v);
            this.markChanged();
        },
        addExternal() {
            if (this.externalTemp) {
                this.form.tujuanExternal.push(this.externalTemp);
                this.externalTemp = '';
                this.markChanged();
            }
        },
        removeExternal(v) {
            this.form.tujuanExternal = this.form.tujuanExternal.filter(x => x !== v);
            this.markChanged();
        },
        addParticipantManual() {
            if (!this.manual.nama) return;
            const nip = this.manual.nip || ('M-' + Date.now());
            if (this.participants.find(p => p.nip === nip)) { this.notify('Peserta sudah ada', 'error'); return; }
            this.participants.push({ nama: this.manual.nama, nip: nip, jabatan: this.manual.jabatan || '-', status: 'aktif' });
            this.manual = { nama: '', nip: '', jabatan: '' };
            this.markChanged();
        },
        toggleStatus(p) {
            p.status = p.status === 'aktif' ? 'nonaktif' : 'aktif';
            this.markChanged();
        },
        removeParticipant(p) {
            this.participants = this.participants.filter(x => x !== p);
            this.markChanged();
        },
        searchParticipants() { this.debounce('participants', async () => { try { const r = await fetch(`/unit-kerja/api/participants?q=${encodeURIComponent(this.participantSearch)}`); const j = await r.json(); if (j.success) { /* auto-suggest placeholder */ } } catch (e) {} }, 400); },
        async uploadAttachment() {
            const input = this.$refs.attachmentInput;
            if (!input || !input.files.length) return;
            const file = input.files[0];
            const fd = new FormData();
            fd.append('file', file);
            try {
                const r = await fetch('/unit-kerja/api/attachments/temp', { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf() }, body: fd });
                const j = await r.json();
                if (j.success) {
                    this.attachments.push({ id: j.data.id, nama: j.data.nama, size: j.data.size_human, path: j.data.path });
                    input.value = '';
                    this.markChanged();
                    if (window.feather) feather.replace();
                } else { this.notify(j.message || 'Gagal upload', 'error'); }
            } catch (e) { this.notify('Gagal upload', 'error'); }
        },
        removeAttachment(i) {
            this.attachments.splice(i, 1);
            this.markChanged();
        },
        setSigner(s) {
            this.signer = s;
            this.closeAll();
            this.markChanged();
        },
        decrementSeq() {
            if (this.form.nomor.seq > 1) {
                this.form.nomor.seq--;
                this.markChanged();
            }
        },
        incrementSeq() {
            this.form.nomor.seq = (this.form.nomor.seq || 0) + 1;
            this.markChanged();
        },
        onLetterTypeChange() {
            const lt = this.letterTypes.find(t => t.id == this.form.letter_type_id);
            this.form.jenis = lt ? lt.name : '';
            if (!this.form.nomor.prefix && lt) {
                if (lt.code) this.form.nomor.prefix = `UB/R-${lt.code.toUpperCase()}`;
                else if (lt.name) this.form.nomor.prefix = lt.name.split(/\s+/).map(w => w[0]).join('').substring(0, 4).toUpperCase();
            }
            this.numberingPreview = null;
            this.previewNumber();
            this.markChanged();
        },
        buildLocalNumber() {
            const p = this.form.nomor.prefix?.trim();
            const sRaw = this.form.nomor.seq;
            const u = this.form.nomor.unit?.trim();
            const y = this.form.nomor.tahun;
            if (!p && !sRaw && !u) return '';
            const pad = (v) => (v == null || v === '') ? '' : String(v).padStart(3, '0');
            const parts = [];
            if (p) parts.push(p);
            if (sRaw) parts.push(pad(sRaw));
            if (u) parts.push(u.toUpperCase());
            if (y) parts.push(y);
            return parts.join('/');
        },
        computedNumber() {
            const local = this.buildLocalNumber();
            if (local) return local;
            if (this.numberingPreview && this.numberingPreview.preview) return this.numberingPreview.preview;
            if (!this.form.letter_type_id) return '— Pilih jenis surat —';
            return 'Memuat nomor...';
        },
        async previewNumber() { if (!this.form.letter_type_id) return; try { const p = new URLSearchParams({ letter_type_id: this.form.letter_type_id, department_id: this.userDepartmentId || '' }); if (this.form.nomor.prefix) p.append('prefix', this.form.nomor.prefix); if (this.form.nomor.suffix) p.append('suffix', this.form.nomor.suffix); const r = await fetch(`/unit-kerja/api/letters/number/preview?${p.toString()}`); const j = await r.json(); if (j.success) { this.numberingPreview = j.data; if (this.form.nomor.seq === null) this.form.nomor.seq = j.data.sequence_next; } } catch (e) {} },
        async submit() {
            if (this.loadingSubmit) return;
            // Basic client validations
            if (!this.form.letter_type_id) { this.notify('Pilih jenis surat terlebih dahulu', 'error'); return; }
            if (!this.form.perihal) { this.notify('Perihal wajib diisi', 'error'); return; }
            if (!this.form.tanggal) { this.notify('Tanggal surat wajib diisi', 'error'); return; }
            if (!this.signer || !this.signer.id) { this.notify('Pilih penandatangan terlebih dahulu', 'error'); return; }

            const payload = {
                letter_type_id: this.form.letter_type_id,
                perihal: this.form.perihal,
                tanggal: this.form.tanggal,
                prioritas: this.form.prioritas,
                klasifikasi: this.form.klasifikasi,
                ringkasan: this.form.ringkasan,
                konten: this.form.konten,
                catatanInternal: this.form.catatanInternal,
                tujuanInternal: this.form.tujuanInternal,
                tujuanExternal: this.form.tujuanExternal,
                participants: this.participants,
                signer_user_id: this.signer.id,
                department_id: this.userDepartmentId,
                prefix: this.form.nomor.prefix,
                suffix: this.form.nomor.suffix,
                attachments: (this.attachments || []).map(a => ({ nama: a.nama, path: a.path }))
            };

            try {
                this.loadingSubmit = true;
                const r = await fetch('/unit-kerja/api/letters/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrf()
                    },
                    body: JSON.stringify(payload)
                });
                const j = await r.json().catch(() => ({ success: false, message: 'Gagal mengajukan (invalid response)' }));
                if (!r.ok || !j.success) throw new Error(j.message || 'Gagal mengajukan surat');

                this.notify('Surat diajukan untuk tanda tangan.');
                this.draftId = j.data?.id || null;
                this.changed = false;
                this.closeAll();
            } catch (e) {
                this.notify(e.message || 'Gagal mengajukan surat', 'error');
            } finally {
                this.loadingSubmit = false;
            }
        },
        resetForm() {
            if (!confirm('Reset form?')) return;
            this.draftId = null;
            this.signer = null;
            this.attachments = [];
            this.participants = [];
            this.numberingPreview = null;
            this.flash = null;
            this.error = null;
            this.form = { letter_type_id: '', jenis: '', perihal: '', tanggal: (new Date()).toISOString().slice(0, 10), prioritas: 'normal', klasifikasi: 'biasa', ringkasan: '', konten: '', catatanInternal: '', tujuanInternal: [], tujuanExternal: [], nomor: { prefix: '', seq: null, unit: '', tahun: (new Date()).getFullYear(), suffix: '' } };
            this.changed = false;
            if (this.letterTypes.length) {
                this.form.letter_type_id = this.letterTypes[0].id;
                this.onLetterTypeChange();
            }
        },
        editor: null,
        init() {
            if (!this.letterTypes.length) { this.loadLetterTypes(); } else {
                if (!this.form.letter_type_id && this.letterTypes.length) {
                    this.form.letter_type_id = this.letterTypes[0].id;
                    this.onLetterTypeChange();
                }
            }
            this.fetchSigners();
            this.fetchDepartments();
            this.$watch('form.nomor.prefix', () => this.debounce('preview', () => this.previewNumber(), 350));
            this.$watch('form.nomor.suffix', () => this.debounce('preview', () => this.previewNumber(), 350));
            if (window.feather) feather.replace();
            this.initEditor();
        },
        initEditor(retry = 0) {
            const mountEl = this.$refs.ckeditorRoot;
            if (!mountEl) return;
            if (this.editor) return;
            if (!window.CKEDITOR) {
                if (retry < 5) {
                    setTimeout(() => this.initEditor(retry + 1), 250);
                } else {
                    console.warn('CKEditor 4 global not found after retries');
                }
                return;
            }
            const initialData = this.form.konten || '<p><br></p>';
            mountEl.innerHTML = initialData;
            const config = {
                removePlugins: 'elementspath,resize',
                toolbar: [
                    { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'RemoveFormat'] },
                    { name: 'paragraph', items: ['NumberedList', 'BulletedList', 'Blockquote'] },
                    { name: 'links', items: ['Link', 'Unlink'] },
                    { name: 'insert', items: ['Table'] },
                    { name: 'styles', items: ['Format'] },
                    { name: 'tools', items: ['Maximize'] },
                    { name: 'clipboard', items: ['Undo', 'Redo'] }
                ],
                allowedContent: true,
                autoParagraph: true,
                startupFocus: false
            };
            try {
                this.editor = CKEDITOR.inline(mountEl, config);
                this.editor.on('change', () => {
                    this.form.konten = this.editor.getData();
                    this.updateContent();
                });
            } catch (e) {
                console.error('CKEditor 4 init error', e);
            }
        },
        selectTemplate(template) {
            // Langsung set konten dari template tanpa fetch
            if (this.editor) {
                const kontenTemplate = this.formatTemplateContent(template.isi);
                this.editor.setData(kontenTemplate);
                this.form.konten = kontenTemplate;
                this.markChanged();
            }
            // Tutup modal
            this.closeAll();
            // Tampilkan notifikasi
            this.notify('Template berhasil diterapkan');
        },
        formatTemplateContent(isi) {
            // Format konten template menjadi HTML yang lebih baik
            // Pisahkan berdasarkan baris kosong (paragraf)
            let paragraphs = isi.split('\n\n');
            let html = '';
            
            paragraphs.forEach(para => {
                para = para.trim();
                if (para === '') return;
                
                // Highlight text dalam kurung siku
                para = para.replace(/\[([^\]]+)\]/g, '<strong>[$1]</strong>');
                
                // Replace newline tunggal dengan <br>
                para = para.replace(/\n/g, '<br>');
                
                html += `<p>${para}</p>`;
            });
            
            return html;
        }
    }" x-init="init()"
        @keydown.escape.window="closeAll()">
        @php
            $priorities = [
                'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
                'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
            ];
        @endphp

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/40">
                        <i data-feather="edit-3" class="w-6 h-6"></i>
                    </span>
                    Buat Surat
                </h1>
            </div>
            <div class="flex items-center gap-2">
                <!-- Draft di-skip sementara -->
                <button @click="open('showPreview')"
                    class="btn bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium px-4 py-2 flex items-center gap-2">
                    <i data-feather="eye" class="w-4 h-4"></i> Pratinjau
                </button>
                <button @click="open('showSubmitConfirm')"
                    class="btn bg-orange-600 hover:bg-orange-500 text-white text-xs font-medium px-4 py-2 flex items-center gap-2">
                    <i data-feather="send" class="w-4 h-4"></i> Ajukan TTD
                </button>
                <button @click="resetForm()"
                    class="btn bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-3 py-2 text-xs font-medium">
                    Reset
                </button>
            </div>
        </div>

        <form @submit.prevent="open('showSubmitConfirm')" class="space-y-8">
            <!-- Informasi Umum -->
            <div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div
                    class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                            data-feather="info" class="w-4 h-4 text-orange-500"></i> Informasi Umum</h2>
                    <span class="text-[11px] text-gray-400" x-show="changed" x-transition>Belum disimpan</span>
                </div>
                <div class="p-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3 text-sm">
                    <div class="flex flex-col gap-1.5 md:col-span-1">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Jenis Surat</label>
                        <select x-model="form.letter_type_id" @change="onLetterTypeChange(); markChanged()"
                            class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="">Pilih Jenis</option>
                            <template x-for="t in letterTypes" :key="t.id">
                                <option :value="t.id" x-text="t.name"></option>
                            </template>
                        </select>
                        <p class="text-[10px] text-gray-400 mt-1" x-show="!letterTypes.length">Tidak ada jenis surat
                            aktif.</p>
                    </div>
                    <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-2">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Perihal</label>
                        <input type="text" x-model="form.perihal" @input="markChanged()"
                            placeholder="Perihal / Subject"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Tanggal Surat</label>
                        <input type="date" x-model="form.tanggal" @change="markChanged()"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Prioritas</label>
                        <select x-model="form.prioritas" @change="markChanged()"
                            class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="normal">Normal</option>
                            <option value="urgent">Urgent</option>
                            <option value="high">High</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div class="flex flex-col gap-1.5">
                        <label
                            class="text-xs font-medium text-gray-600 dark:text-gray-300 flex items-center gap-1">Tingkat
                            Kerahasiaan <span class="text-[10px] text-gray-400 font-normal">(Opsional)</span></label>
                        <select x-model="form.klasifikasi" @change="markChanged()"
                            class="rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="biasa">Biasa</option>
                            <option value="internal">Internal</option>
                            <option value="rahasia">Rahasia</option>
                        </select>
                    </div>
                    {{-- <div class="flex flex-col gap-1.5 md:col-span-2 lg:col-span-3">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Ringkasan Singkat</label>
                        <textarea rows="2" x-model="form.ringkasan" @input="markChanged()"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500"
                            placeholder="Ringkasan isi pokok surat..."></textarea>
                    </div> --}}
                </div>
            </div>

            <!-- Penomoran & Penandatangan -->
            <div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div
                    class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                            data-feather="hash" class="w-4 h-4 text-orange-500"></i> Nomor & Penandatangan</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="open('showNumbering')"
                            class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i
                                data-feather='sliders' class='w-3.5 h-3.5'></i> Atur Penomoran</button>
                        <button type="button" @click="open('showSigner')"
                            class="text-[11px] px-3 py-1.5 rounded bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-1"><i
                                data-feather='user-check' class='w-3.5 h-3.5'></i> Penandatangan</button>
                    </div>
                </div>
                <div class="p-6 grid gap-6 md:grid-cols-2 text-sm">
                    <div class="space-y-2">
                        <div class="text-xs font-medium text-gray-600 dark:text-gray-300">Nomor Surat (Preview)</div>
                        <div class="px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 border border-dashed border-gray-300 dark:border-gray-600 font-mono text-xs text-orange-600 dark:text-amber-400"
                            x-text="computedNumber()"></div>
                        <p class="text-[10px] text-gray-400">Nomor final akan dikunci saat pengajuan tanda tangan.</p>
                    </div>
                    <div class="space-y-2">
                        <div class="text-xs font-medium text-gray-600 dark:text-gray-300">Penandatangan</div>
                        <template x-if="signer">
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 flex items-center justify-between">
                                <div>
                                    <div class="text-xs font-semibold text-gray-800 dark:text-gray-100"
                                        x-text="signer.nama"></div>
                                    <div class="text-[11px] text-gray-500 dark:text-gray-400" x-text="signer.jabatan">
                                    </div>
                                </div>
                                <button type="button" @click="open('showSigner')"
                                    class="text-[11px] text-orange-600 dark:text-amber-400 hover:underline">Ubah</button>
                            </div>
                        </template>
                        <template x-if="!signer">
                            <button type="button" @click="open('showSigner')"
                                class="w-full p-3 rounded-lg border border-dashed border-gray-300 dark:border-gray-600 text-[11px] text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 justify-center"><i
                                    data-feather='plus-circle' class='w-4 h-4'></i>Pilih Penandatangan</button>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Penerima & Peserta -->
            <div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div
                    class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                            data-feather="users" class="w-4 h-4 text-orange-500"></i> Penerima & Peserta</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="open('showParticipants')"
                            class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i
                                data-feather='user-plus' class='w-3.5 h-3.5'></i> Kelola Peserta</button>
                    </div>
                </div>
                <div class="p-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3 text-sm">
                    <div class="flex flex-col gap-1.5 md:col-span-1">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Unit Internal
                            (Tujuan)</label>
                        <div class="space-y-2">
                            <template x-for="u in form.tujuanInternal" :key="u">
                                <div
                                    class="px-3 py-1.5 rounded bg-gray-50 dark:bg-gray-700 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                                    <span x-text="u"></span>
                                    <button type="button" @click="removeInternal(u)"
                                        class="text-rose-500 hover:text-rose-400"><i data-feather='x'
                                            class='w-3 h-3'></i></button>
                                </div>
                            </template>
                            <div class="flex gap-2">
                                <select x-model="internalTemp"
                                    class="flex-1 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                                    <option value="">Pilih Unit/Departemen</option>
                                    <template x-for="dept in availableDepartments" :key="dept.id">
                                        <option :value="dept.name" x-text="dept.name"></option>
                                    </template>
                                </select>
                                <button type="button" @click="addInternal()"
                                    class="px-3 py-1.5 rounded bg-orange-600 hover:bg-orange-500 text-white text-[11px]">Tambah</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 md:col-span-1">
                        <label class="text-xs font-medium text-gray-600 dark:text-gray-300">Alamat Eksternal</label>
                        <div class="space-y-2">
                            <template x-for="e in form.tujuanExternal" :key="e">
                                <div
                                    class="px-3 py-1.5 rounded bg-gray-50 dark:bg-gray-700 flex items-center justify-between text-xs text-gray-600 dark:text-gray-300">
                                    <span x-text="e"></span>
                                    <button type="button" @click="removeExternal(e)"
                                        class="text-rose-500 hover:text-rose-400"><i data-feather='x'
                                            class='w-3 h-3'></i></button>
                                </div>
                            </template>
                            <div class="flex gap-2">
                                <input type="text" x-model="externalTemp" @keydown.enter.prevent="addExternal()"
                                    placeholder="Tambah alamat"
                                    class="flex-1 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
                                <button type="button" @click="addExternal()"
                                    class="px-3 py-1.5 rounded bg-orange-600 hover:bg-orange-500 text-white text-[11px]">Tambah</button>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-1.5 md:col-span-2 lg:grid-cols-1">
                        <label
                            class="text-xs font-medium text-gray-600 dark:text-gray-300 flex items-center gap-1">Statistik
                            Peserta <span class="text-[10px] text-gray-400 font-normal">(otomatis)</span></label>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 text-center">
                                <div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">
                                    Peserta</div>
                                <div class="text-lg font-semibold text-orange-600 dark:text-amber-400"
                                    x-text="participants.length"></div>
                            </div>
                            <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-700 text-center">
                                <div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">
                                    Lampiran</div>
                                <div class="text-lg font-semibold text-amber-600 dark:text-amber-400"
                                    x-text="attachments.length"></div>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button type="button" @click="open('showAttachments')"
                                class="flex-1 text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1 justify-center"><i
                                    data-feather='paperclip' class='w-3.5 h-3.5 text-orange-500'></i>
                                Lampiran</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Konten Surat -->
            <div class="bg-white dark:bg-gray-800 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700">
                <div
                    class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                            data-feather="file-text" class="w-4 h-4 text-orange-500"></i> Konten Surat</h2>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="open('showTemplates')"
                            class="text-[11px] px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i
                                data-feather='layers' class='w-3.5 h-3.5 text-orange-500'></i> Template</button>
                    </div>
                </div>
                <div class="p-6 space-y-4 text-sm">
                    <div>
                        <div class="relative">
                            <div x-show="!(form.konten||'').length"
                                class="absolute inset-0 pointer-events-none flex items-start">
                                <p class="text-xs text-gray-400 px-3 py-2">Tulis isi surat di sini...</p>
                            </div>
                            <div id="editor" x-ref="ckeditorRoot"
                                class="min-h-[220px] border border-dashed border-gray-300 dark:border-gray-600 rounded-md px-3 py-2 bg-white dark:bg-gray-800 focus:outline-none"
                                contenteditable="true"></div>
                        </div>
                        <textarea x-show="false" x-model="form.konten"></textarea>
                        <div class="mt-2 text-[10px] text-gray-400" x-text="contentLength() + ' karakter'"></div>
                    </div>
                </div>
            </div>

            <!-- Aksi Bawah -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 pt-2">
                <div class="text-[11px] text-gray-500 dark:text-gray-400 flex items-center gap-3">
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="open('showPreview')"
                        class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-2"><i
                            data-feather='eye' class='w-4 h-4'></i>Pratinjau</button>
                    <button type="submit"
                        class="px-4 py-2 rounded-lg text-sm bg-orange-600 hover:bg-orange-500 text-white flex items-center gap-2"><i
                            data-feather='send' class='w-4 h-4'></i>Ajukan TTD</button>
                </div>
            </div>
        </form>

        @include('pages.unit_kerja.buat-surat.detail.preview-modal')
        @include('pages.unit_kerja.buat-surat.detail.participants-modal')
        @include('pages.unit_kerja.buat-surat.detail.attachment-modal')
        @include('pages.unit_kerja.buat-surat.detail.templates-modal')
        @include('pages.unit_kerja.buat-surat.detail.signer-modal')
        @include('pages.unit_kerja.buat-surat.detail.numbering-modal')
        @include('pages.unit_kerja.buat-surat.detail.submit-confirm-modal')

        <div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat ·
            Universitas Bakrie</div>
    </div>
</x-app-layout>
