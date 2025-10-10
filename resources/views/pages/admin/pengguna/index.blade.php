<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="usersPage()" x-init="init()">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
                        <i data-feather="users" class="w-5 h-5"></i>
                    </span>
                    Manajemen Pengguna
                </h1>
            </div>
            <button @click="showAdd = true"
                class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
                <i data-feather="user-plus" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Tambah Pengguna</span>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow ring-1 ring-gray-200 dark:ring-gray-700 p-6 relative">
            <div class="mb-6 flex flex-wrap gap-4 items-center">
                <div class="relative w-full sm:max-w-xs">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <i data-feather="search" class="w-4 h-4 text-gray-400 dark:text-gray-400"></i>
                    </span>
                    <input type="text" x-model="filters.search" @keyup.enter="applyFilters()"
                        class="bg-gray-50 border border-gray-200 text-gray-700 placeholder-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400 text-sm rounded-lg pl-10 p-2.5 w-full focus:outline-none focus:ring-2 focus:ring-orange-400 focus:border-orange-400 dark:focus:ring-orange-500 dark:focus:border-orange-500"
                        placeholder="Cari nama, email, atau NIP (Enter untuk cari)">
                </div>
                <select x-model="filters.role"
                    class="text-sm rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                    <option value="">Semua Role</option>
                    <option value="admin">Admin</option>
                    <option value="rektorat">Rektorat</option>
                    <option value="unit_kerja">Unit Kerja</option>
                </select>
                <select x-model="filters.status"
                    class="text-sm rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
                <button @click="applyFilters()"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-600 hover:bg-amber-500 text-white text-sm shadow-sm">
                    <i data-feather="filter" class="w-4 h-4"></i>
                    Terapkan
                </button>
                <button @click="resetFilters()"
                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i data-feather="rotate-ccw" class="w-4 h-4"></i>
                    Reset
                </button>
                <div class="ml-auto flex items-center gap-3 text-xs" x-show="meta.total">
                    <span class="text-gray-500 dark:text-gray-400" x-text="'Total: ' + meta.total"></span>
                </div>
            </div>

            <template x-if="error">
                <div class="mb-4 px-4 py-2 rounded-lg bg-rose-50 text-rose-600 text-sm dark:bg-rose-500/10 dark:text-rose-300"
                    x-text="error"></div>
            </template>
            <template x-if="flash">
                <div
                    class="mb-4 px-4 py-2 rounded-lg bg-emerald-50 text-emerald-700 text-sm dark:bg-emerald-500/10 dark:text-emerald-300 flex items-center justify-between">
                    <span x-text="flash"></span>
                    <button @click="flash=null" class="text-emerald-500 hover:text-emerald-700 dark:text-emerald-300"><i
                            data-feather='x' class='w-4 h-4'></i></button>
                </div>
            </template>

            <div class="overflow-x-auto rounded-lg ring-1 ring-gray-100 dark:ring-gray-700/60">
                <table class="w-full">
                    <thead>
                        <tr
                            class="bg-gray-50 dark:bg-gray-700/50 text-left text-gray-600 dark:text-gray-200 text-xs uppercase tracking-wide">
                            <th class="px-4 py-3 rounded-l-lg font-semibold">No.</th>
                            <th class="px-4 py-3 font-semibold">Nama</th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">NIP</th>
                            <th class="px-4 py-3 font-semibold">Departemen</th>
                            <th class="px-4 py-3 font-semibold">Role</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                            <th class="px-4 py-3 rounded-r-lg font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-if="loading">
                            <tr>
                                <td colspan="8"
                                    class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">Memuat
                                    data...</td>
                            </tr>
                        </template>
                        <template x-if="!loading && users.length===0">
                            <tr>
                                <td colspan="8"
                                    class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Tidak ada
                                    data pengguna.</td>
                            </tr>
                        </template>
                        <template x-for="(u, idx) in users" :key="u.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                :class="u._deleting ? 'opacity-50 pointer-events-none' : ''">
                                <td class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400" x-text="meta.from + idx">
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100" x-text="u.name"></td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="u.email"></td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="u.nip || '-' "></td>
                                <td class="px-4 py-2 text-gray-600 dark:text-gray-300"
                                    x-text="u.department?.name || '-' "></td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold"
                                        :class="u.roleClass" x-text="u.role"></span>
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold"
                                        :class="u.statusClass" x-text="u.statusLabel"></span>
                                </td>
                                <td class="px-4 py-2 flex gap-2">
                                    <button @click="openView(u)"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                        title="Lihat Detail">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="openEdit(u)"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500"
                                        title="Edit">
                                        <i data-feather="edit-2" class="w-4 h-4"></i>
                                    </button>
                                    <button @click="openDelete(u)"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-rose-500"
                                        title="Hapus">
                                        <i data-feather="trash" class="w-4 h-4"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mt-4 text-xs" x-show="meta.total > meta.per_page">
                <button
                    class="px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 disabled:opacity-40"
                    :disabled="!meta.prev_page_url" @click="changePage(meta.current_page - 1)">Prev</button>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300"
                        x-text="'Hal ' + meta.current_page + ' / ' + meta.last_page"></span>
                </div>
                <button
                    class="px-3 py-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 disabled:opacity-40"
                    :disabled="!meta.next_page_url" @click="changePage(meta.current_page + 1)">Next</button>
            </div>
        </div>

        @include('pages.admin.pengguna.detail.add-modal')
        @include('pages.admin.pengguna.detail.edit-modal')
        @include('pages.admin.pengguna.detail.view-modal')
        @include('pages.admin.pengguna.detail.delete-modal')
    </div>
</x-app-layout>

<script>
    function usersPage() {
        return {
            users: [],
            departments: [],
            meta: {
                total: 0,
                per_page: 20,
                current_page: 1,
                last_page: 1,
                from: 0,
                next_page_url: null,
                prev_page_url: null
            },
            loading: false,
            error: null,
            flash: null,
            showAdd: false,
            showEdit: false,
            showView: false,
            showDelete: false,
            selectedUser: null,
            formAdd: {
                name: '',
                email: '',
                nip: '',
                department_id: '',
                role: 'unit_kerja',
                status: 'active',
                password: ''
            },
            filters: {
                search: '',
                role: '',
                status: ''
            },
            init() {
                this.loadDepartments().then(() => this.reload());
            },
            csrf() {
                const el = document.querySelector('meta[name="csrf-token"]');
                return el ? el.getAttribute('content') : '';
            },
            apiUrl(page = 1) {
                const p = new URLSearchParams();
                if (this.filters.search) p.append('search', this.filters.search);
                if (this.filters.role) p.append('role', this.filters.role);
                if (this.filters.status) p.append('status', this.filters.status);
                p.append('page', page);
                return `/admin/users?${p.toString()}`;
            },
            transform(u) {
                if (u.department_id && !u.department && this.departments.length) {
                    const d = this.departments.find(dd => dd.id === u.department_id);
                    if (d) u.department = d;
                }
                u.statusLabel = u.status === 'active' ? 'Aktif' : 'Nonaktif';
                u.statusClass = u.status === 'active' ?
                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' :
                    'bg-gray-200 text-gray-700 dark:bg-gray-600/40 dark:text-gray-300';
                u.roleClass = u.role === 'admin' ?
                    'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300' :
                    (u.role === 'rektorat' ? 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300' :
                        'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300');
                return u;
            },
            async loadDepartments() {
                try {
                    const res = await fetch('/admin/departments');
                    if (res.ok) {
                        this.departments = await res.json();
                    }
                } catch (e) {}
            },
            async reload() {
                this.loading = true;
                this.error = null;
                try {
                    const res = await fetch(this.apiUrl(this.meta.current_page));
                    if (!res.ok) throw new Error('Gagal memuat data');
                    const json = await res.json();
                    this.users = (json.data || []).map(this.transform);
                    this.meta = {
                        total: json.total,
                        per_page: json.per_page,
                        current_page: json.current_page,
                        last_page: json.last_page,
                        from: json.from || 0,
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
            async changePage(p) {
                if (p < 1 || p > this.meta.last_page) return;
                this.meta.current_page = p;
                await this.reload();
            },
            applyFilters() {
                this.meta.current_page = 1;
                this.reload();
            },
            resetFilters() {
                this.filters = {
                    search: '',
                    role: '',
                    status: ''
                };
                this.applyFilters();
            },
            resetAdd() {
                this.formAdd = {
                    name: '',
                    email: '',
                    nip: '',
                    department_id: '',
                    role: 'unit_kerja',
                    status: 'active',
                    password: ''
                };
            },
            async storeUser() {
                this.error = null;
                try {
                    const payload = {
                        ...this.formAdd
                    };
                    if (!payload.password) throw new Error('Password wajib diisi');
                    const res = await fetch('/admin/users', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal membuat user');
                    this.flash = 'User berhasil dibuat';
                    this.showAdd = false;
                    this.resetAdd();
                    this.reload();
                } catch (e) {
                    this.error = e.message;
                }
            },
            async openView(u) {
                try {
                    const res = await fetch(`/admin/users/${u.id}`);
                    if (res.ok) {
                        const usr = await res.json();
                        this.selectedUser = this.transform(usr);
                    } else this.selectedUser = u;
                } catch {
                    this.selectedUser = u;
                }
                this.showView = true;
            },
            async openEdit(u) {
                try {
                    const res = await fetch(`/admin/users/${u.id}`);
                    if (res.ok) {
                        const usr = await res.json();
                        u = this.transform(usr);
                    }
                } catch {}
                if (!u.department_id && u.department && u.department.id) u.department_id = u.department.id;
                this.selectedUser = JSON.parse(JSON.stringify(u));
                this.showEdit = true;
            },
            openDelete(u) {
                this.selectedUser = u;
                this.showDelete = true;
            },
            async updateUser(form) {
                this.error = null;
                try {
                    const id = form.id;
                    const payload = {
                        ...form
                    };
                    if (!payload.password) delete payload.password;
                    const res = await fetch(`/admin/users/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal update user');
                    this.flash = 'Perubahan tersimpan';
                    this.showEdit = false;
                    this.selectedUser = null;
                    const idx = this.users.findIndex(x => x.id === json.data.id);
                    if (idx > -1) {
                        this.users.splice(idx, 1, this.transform(json.data));
                    } else {
                        this.reload();
                    }
                } catch (e) {
                    this.error = e.message;
                }
            },
            async deleteUser() {
                if (!this.selectedUser) return;
                const id = this.selectedUser.id;
                try {
                    const target = this.users.find(u => u.id === id);
                    if (target) target._deleting = true;
                    const res = await fetch(`/admin/users/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': this.csrf(),
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (!res.ok) throw new Error(json.message || 'Gagal menghapus user');
                    this.flash = 'User dihapus';
                    this.showDelete = false;
                    this.selectedUser = null;
                    this.users = this.users.filter(u => u.id !== id);
                    if (this.users.length === 0 && this.meta.current_page > 1) {
                        this.changePage(this.meta.current_page - 1);
                    }
                } catch (e) {
                    this.error = e.message;
                    const tgt = this.users.find(u => u.id === id);
                    if (tgt) tgt._deleting = false;
                }
            }
        }
    }
</script>
