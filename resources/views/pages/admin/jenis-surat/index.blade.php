<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="letterTypesPage()" x-init="init()"
        @keydown.escape.window="closeAll()">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
                        <i data-feather="file-text" class="w-5 h-5"></i>
                    </span>
                    Manajemen Jenis Surat
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showAdd = true"
                    class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Tambah Jenis</span>
                </button>
                <button @click="$dispatch('refresh-letter-types')"
                    class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
            <div class="grid gap-4 md:gap-6 md:grid-cols-5 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="search" class="w-4 h-4 text-gray-400"></i>
                        </span>
                        <input type="text" x-model="filters.q" @keyup.enter="applyFilters()"
                            placeholder="Nama atau kode jenis surat"
                            class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kategori
                        (placeholder)</label>
                    <input type="text" x-model="filters.category"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100"
                        placeholder="(belum ada kolom)" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
                    <select x-model="filters.status"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
                        <option value="">Semua</option>
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Format (cari
                        teks)</label>
                    <input type="text" x-model="filters.format"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100"
                        placeholder="Token" />
                </div>
                <div class="flex gap-2">
                    <button @click="applyFilters()" :disabled="loading"
                        class="flex-1 bg-amber-600 hover:bg-amber-500 disabled:opacity-50 text-white text-sm font-medium rounded-lg px-4 py-2 transition inline-flex items-center justify-center gap-2"><i
                            data-feather='filter' class='w-4 h-4'></i> Filter</button>
                    <button @click="resetFilters()" type="button" :disabled="loading"
                        class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 disabled:opacity-50 rounded-lg px-4 py-2 text-sm text-center inline-flex items-center justify-center gap-2"><i
                            data-feather='rotate-ccw' class='w-4 h-4'></i> Reset</button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                        data-feather="grid" class="w-4 h-4 text-amber-500"></i> Daftar Jenis Surat</h2>
                <div class="text-xs text-gray-500 dark:text-gray-400" x-show="meta.total" x-text="'Total: '+meta.total">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Kode</th>
                            <th class="text-left px-5 py-3 font-semibold">Nama Jenis</th>
                            <th class="text-left px-5 py-3 font-semibold">Kategori</th>
                            <th class="text-left px-5 py-3 font-semibold">Format Nomor</th>
                            <th class="text-left px-5 py-3 font-semibold">Status</th>
                            <th class="text-left px-5 py-3 font-semibold">Digunakan</th>
                            <th class="text-right px-5 py-3 font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        <template x-if="loading">
                            <tr>
                                <td colspan="7"
                                    class="px-5 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Memuat
                                    data...</td>
                            </tr>
                        </template>
                        <template x-if="!loading && items.length===0">
                            <tr>
                                <td colspan="7"
                                    class="px-5 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada
                                    data.</td>
                            </tr>
                        </template>
                        <template x-for="t in items" :key="t.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition"
                                :class="t._deleting ? 'opacity-50 pointer-events-none' : ''">
                                <td class="px-5 py-3 font-mono text-xs text-gray-700 dark:text-gray-200"
                                    x-text="t.code"></td>
                                <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100" x-text="t.name"></td>
                                <td class="px-5 py-3"><span
                                        class="px-2 py-0.5 rounded-lg text-[11px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300"
                                        x-text="t.category || '-' "></span></td>
                                <td class="px-5 py-3 text-[11px] font-mono text-gray-500 dark:text-gray-400"
                                    x-text="truncateFormat(t.number_format)"></td>
                                <td class="px-5 py-3"><span class="px-2 py-0.5 rounded-lg text-[11px] font-medium"
                                        :class="t.is_active ?
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' :
                                            'bg-gray-200 text-gray-600 dark:bg-gray-600/40 dark:text-gray-300'"
                                        x-text="t.is_active ? 'Aktif':'Nonaktif'"></span></td>
                                <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300"
                                    x-text="t.used ?? t.letters_count ?? 0"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="openView(t)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                            title="Detail"><i data-feather='eye' class='w-4 h-4'></i></button>
                                        <button @click="openEdit(t)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                            title="Edit"><i data-feather='edit-2' class='w-4 h-4'></i></button>
                                        <button @click="openDelete(t)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-rose-500"
                                            title="Hapus"><i data-feather='trash' class='w-4 h-4'></i></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400"
                x-show="meta.total">
                <span
                    x-text="'Menampilkan ' + meta.from + ' - ' + meta.to + ' dari ' + meta.total + ' jenis surat'"></span>
                <div class="flex gap-1 items-center">
                    <button @click="changePage(meta.current_page-1)" :disabled="!meta.prev_page_url"
                        class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400 disabled:opacity-40"><i
                            data-feather='chevron-left' class='w-3 h-3'></i></button>
                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300"
                        x-text="meta.current_page + ' / ' + meta.last_page"></span>
                    <button @click="changePage(meta.current_page+1)" :disabled="!meta.next_page_url"
                        class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400 disabled:opacity-40"><i
                            data-feather='chevron-right' class='w-3 h-3'></i></button>
                </div>
            </div>
        </div>

        @include('pages.admin.jenis-surat.detail.add-modal')
        @include('pages.admin.jenis-surat.detail.edit-modal')
        @include('pages.admin.jenis-surat.detail.view-modal')
        @include('pages.admin.jenis-surat.detail.delete-modal')

        <div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat ·
            Universitas Bakrie</div>
    </div>
</x-app-layout>

<script>
    function letterTypesPage() {
        return {
            items: [],
            loading: false,
            error: null,
            flash: null,
            filters: {
                q: '',
                category: '',
                status: '',
                format: ''
            },
            meta: {
                total: 0,
                per_page: 15,
                current_page: 1,
                last_page: 1,
                from: 0,
                to: 0,
                next_page_url: null,
                prev_page_url: null
            },
            showAdd: false,
            showEdit: false,
            showView: false,
            showDelete: false,
            selected: null,
            formAdd: {
                name: '',
                code: '',
                number_format: '',
                is_active: 1,
                description: ''
            },
            formEdit: {
                id: null,
                name: '',
                code: '',
                number_format: '',
                is_active: 1,
                description: ''
            },
            csrf() {
                const el = document.querySelector('meta[name="csrf-token"]');
                return el ? el.content : '';
            },
            apiUrl(page = 1) {
                const p = new URLSearchParams();
                p.append('page', page);
                if (this.filters.q) p.append('q', this.filters.q);
                if (this.filters.status !== '') p.append('status', this.filters.status);
                if (this.filters.category) p.append('category', this.filters.category);
                if (this.filters.format) p.append('format', this.filters.format);
                return `/admin/letter-types?${p.toString()}`;
            },
            async reload() {
                this.loading = true;
                this.error = null;
                try {
                    const res = await fetch(this.apiUrl(this.meta.current_page));
                    if (!res.ok) throw new Error('Gagal memuat data');
                    const json = await res.json();
                    this.items = json.data || [];
                    this.meta = {
                        total: json.total,
                        per_page: json.per_page,
                        current_page: json.current_page,
                        last_page: json.last_page,
                        from: json.from || 0,
                        to: json.to || 0,
                        next_page_url: json.next_page_url,
                        prev_page_url: json.prev_page_url
                    };
                    this.$nextTick(() => {
                        if (window.feather) feather.replace();
                    });
                } catch (e) {
                    this.error = e.message;
                } finally {
                    this.loading = false;
                }
            },
            init() {
                this.reload();
            },
            applyFilters() {
                this.meta.current_page = 1;
                this.reload();
            },
            resetFilters() {
                this.filters = {
                    q: '',
                    category: '',
                    status: '',
                    format: ''
                };
                this.applyFilters();
            },
            changePage(p) {
                if (p < 1 || p > this.meta.last_page) return;
                this.meta.current_page = p;
                this.reload();
            },
            closeAll() {
                this.showAdd = false;
                this.showEdit = false;
                this.showView = false;
                this.showDelete = false;
            },
            openView(t) {
                this.selected = t;
                this.showView = true;
            },
            openEdit(t) {
                this.formEdit = {
                    id: t.id,
                    name: t.name,
                    code: t.code,
                    number_format: t.number_format,
                    is_active: t.is_active ? 1 : 0,
                    description: t.description || ''
                };
                this.selected = t;
                this.showEdit = true;
            },
            openDelete(t) {
                this.selected = t;
                this.showDelete = true;
            },
            truncateFormat(f) {
                if (!f) return '-';
                return f.length > 34 ? f.slice(0, 32) + '…' : f;
            },
            async storeType() {
                this.error = null;
                try {
                    const payload = {
                        ...this.formAdd
                    };
                    const res = await fetch('/admin/letter-types', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal menyimpan');
                    this.flash = 'Jenis surat dibuat';
                    this.showAdd = false;
                    this.formAdd = {
                        name: '',
                        code: '',
                        number_format: '',
                        is_active: 1,
                        description: ''
                    };
                    this.reload();
                } catch (e) {
                    this.error = e.message;
                }
            },
            async updateType() {
                if (!this.formEdit.id) return;
                this.error = null;
                try {
                    const id = this.formEdit.id;
                    const payload = {
                        ...this.formEdit
                    };
                    const res = await fetch(`/admin/letter-types/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal update');
                    this.flash = 'Perubahan tersimpan';
                    this.showEdit = false;
                    const idx = this.items.findIndex(x => x.id === json.data.id);
                    if (idx > -1) {
                        this.items.splice(idx, 1, json.data);
                    } else {
                        this.reload();
                    }
                } catch (e) {
                    this.error = e.message;
                }
            },
            async deleteType() {
                if (!this.selected) return;
                const id = this.selected.id;
                try {
                    const target = this.items.find(i => i.id === id);
                    if (target) target._deleting = true;
                    const res = await fetch(`/admin/letter-types/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal hapus');
                    this.flash = 'Jenis surat dihapus';
                    this.showDelete = false;
                    this.items = this.items.filter(i => i.id !== id);
                    if (this.items.length === 0 && this.meta.current_page > 1) this.changePage(this.meta
                        .current_page - 1);
                } catch (e) {
                    this.error = e.message;
                    const t = this.items.find(i => i.id === id);
                    if (t) t._deleting = false;
                }
            },
        }
    }
</script>
