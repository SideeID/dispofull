<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="departmentsPage()" x-init="init()"
        @keydown.escape.window="closeAll()">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
                        <i data-feather="layers" class="w-5 h-5"></i>
                    </span>
                    Manajemen Departemen
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <button @click="open('showAdd')"
                    class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
                    <i data-feather="plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Tambah Departemen</span>
                </button>
                <button @click="$dispatch('refresh-departments')"
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
                            placeholder="Nama atau kode departemen"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 pl-10 pr-4 py-2 text-sm text-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tipe</label>
                    <select x-model="filters.type"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
                        <option value="">Semua</option>
                        <option value="rektorat">Rektorat</option>
                        <option value="unit_kerja">Unit Kerja</option>
                    </select>
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
                <div class="flex gap-2">
                    <button @click="applyFilters()" :disabled="loading"
                        class="flex-1 bg-amber-600 disabled:opacity-50 hover:bg-amber-500 text-white text-sm font-medium rounded-lg px-4 py-2 transition inline-flex items-center justify-center gap-2">
                        <i data-feather="filter" class="w-4 h-4"></i> Filter
                    </button>
                    <button @click="resetFilters()" type="button" :disabled="loading"
                        class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-sm inline-flex items-center justify-center gap-2 disabled:opacity-50">
                        <i data-feather="rotate-ccw" class="w-4 h-4"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow ring-1 ring-gray-200 dark:ring-gray-700" x-cloak>
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                        data-feather="grid" class="w-4 h-4 text-amber-500"></i> Daftar Departemen</h2>
                <div class="text-xs text-gray-500 dark:text-gray-400" x-show="meta.total" x-text="'Total: '+meta.total">
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="px-5 py-3 text-left font-medium">Kode</th>
                            <th class="px-5 py-3 text-left font-medium">Nama</th>
                            <th class="px-5 py-3 text-left font-medium">Tipe</th>
                            <th class="px-5 py-3 text-left font-medium">Status</th>
                            <th class="px-5 py-3 text-left font-medium">Surat Masuk</th>
                            <th class="px-5 py-3 text-left font-medium">Surat Keluar</th>
                            <th class="px-5 py-3 text-left font-medium">Aksi</th>
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
                        <template x-for="d in items" :key="d.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition"
                                :class="d._deleting ? 'opacity-50 pointer-events-none' : ''">
                                <td class="px-5 py-3 font-mono text-xs text-gray-500 dark:text-gray-400"
                                    x-text="d.code"></td>
                                <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100" x-text="d.name"></td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-medium"
                                        :class="d.type === 'rektorat' ?
                                            'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                                            'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300'"
                                        x-text="capitalize(d.type)"></span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-medium"
                                        :class="d.is_active ?
                                            'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' :
                                            'bg-gray-200 text-gray-700 dark:bg-gray-600/40 dark:text-gray-300'"
                                        x-text="d.is_active ? 'Aktif' : 'Nonaktif'"></span>
                                </td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="d.in_count ?? 0"></td>
                                <td class="px-5 py-3 text-gray-700 dark:text-gray-300" x-text="d.out_count ?? 0"></td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        <button @click="openView(d)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                            title="Detail"><i data-feather="eye" class="w-4 h-4"></i></button>
                                        <button @click="openEdit(d)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                            title="Edit"><i data-feather="edit-2" class="w-4 h-4"></i></button>
                                        <button @click="openDelete(d)"
                                            class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-rose-500"
                                            title="Hapus"><i data-feather="trash" class="w-4 h-4"></i></button>
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
                    x-text="'Menampilkan ' + meta.from + ' - ' + meta.to + ' dari ' + meta.total + ' departemen'"></span>
                <div class="flex gap-1 items-center">
                    <button @click="changePage(meta.current_page-1)" :disabled="!meta.prev_page_url"
                        class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400 disabled:opacity-40"><i
                            data-feather="chevron-left" class="w-3 h-3"></i></button>
                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300"
                        x-text="meta.current_page + ' / ' + meta.last_page"></span>
                    <button @click="changePage(meta.current_page+1)" :disabled="!meta.next_page_url"
                        class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400 disabled:opacity-40"><i
                            data-feather="chevron-right" class="w-3 h-3"></i></button>
                </div>
            </div>
        </div>

        @include('pages.admin.departemen.detail.add-modal')
        @include('pages.admin.departemen.detail.edit-modal')
        @include('pages.admin.departemen.detail.view-modal')
        @include('pages.admin.departemen.detail.delete-modal')

        <div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat ·
            Universitas Bakrie</div>
    </div>
</x-app-layout>

<script>
    function departmentsPage() {
        return {
            items: [],
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
            loading: false,
            error: null,
            flash: null,
            filters: {
                q: '',
                type: '',
                status: ''
            },
            showAdd: false,
            showEdit: false,
            showView: false,
            showDelete: false,
            selected: null,
            formAdd: {
                name: '',
                code: '',
                type: 'unit_kerja',
                is_active: 1,
                description: ''
            },
            formEdit: {
                id: null,
                name: '',
                code: '',
                type: 'unit_kerja',
                is_active: 1,
                description: ''
            },
            init() {
                this.reload();
            },
            csrf() {
                const el = document.querySelector('meta[name="csrf-token"]');
                return el ? el.getAttribute('content') : '';
            },
            apiUrl(page = 1) {
                const p = new URLSearchParams();
                p.append('manage', '1');
                p.append('page', page);
                if (this.filters.q) p.append('q', this.filters.q);
                if (this.filters.type) p.append('type', this.filters.type);
                if (this.filters.status !== '' && this.filters.status != null) p.append('status', this.filters.status);
                return `/admin/departments?${p.toString()}`;
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
            applyFilters() {
                this.meta.current_page = 1;
                this.reload();
            },
            resetFilters() {
                this.filters = {
                    q: '',
                    type: '',
                    status: ''
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
            openView(d) {
                this.selected = d;
                this.showView = true;
            },
            openEdit(d) {
                this.formEdit = {
                    id: d.id,
                    name: d.name,
                    code: d.code,
                    type: d.type,
                    is_active: d.is_active ? 1 : 0,
                    description: d.description || ''
                };
                this.selected = d;
                this.showEdit = true;
            },
            openDelete(d) {
                this.selected = d;
                this.showDelete = true;
            },
            async storeDepartment() {
                this.error = null;
                try {
                    const payload = {
                        ...this.formAdd
                    };
                    const res = await fetch('/admin/departments/manage', {
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
                    this.flash = 'Departemen dibuat';
                    this.showAdd = false;
                    this.formAdd = {
                        name: '',
                        code: '',
                        type: 'unit_kerja',
                        is_active: 1,
                        description: ''
                    };
                    this.reload();
                } catch (e) {
                    this.error = e.message;
                }
            },
            async updateDepartment() {
                if (!this.formEdit.id) return;
                this.error = null;
                try {
                    const id = this.formEdit.id;
                    const payload = {
                        ...this.formEdit
                    };
                    const res = await fetch(`/admin/departments/manage/${id}`, {
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
                    this.showEdit = false; // update local
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
            async deleteDepartment() {
                if (!this.selected) return;
                const id = this.selected.id;
                try {
                    const target = this.items.find(i => i.id === id);
                    if (target) target._deleting = true;
                    const res = await fetch(`/admin/departments/manage/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal hapus');
                    this.flash = 'Departemen dihapus';
                    this.showDelete = false;
                    this.items = this.items.filter(i => i.id !== id);
                    if (this.items.length === 0 && this.meta.current_page > 1) {
                        this.changePage(this.meta.current_page - 1);
                    }
                } catch (e) {
                    this.error = e.message;
                    const t = this.items.find(i => i.id === id);
                    if (t) t._deleting = false;
                }
            },
            capitalize(s) {
                if (!s) return '';
                return s.charAt(0).toUpperCase() + s.slice(1);
            }
        }
    }
</script>
