<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="{
        showDetail: false,
        showResponse: false,
        selected: null,
        open(modal, row = null) {
            this[modal] = false;
            this.selected = row;
            this.$nextTick(() => { this[modal] = true; });
        },
        closeAll() { 
            this.showDetail = false;
            this.showResponse = false;
        },
    }"
    @keydown.escape.window="closeAll()"
    @open-modal="open($event.detail.modal, $event.detail.row)"
    @open-modal.window="open($event.detail.modal, $event.detail.row)">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-indigo-500 via-purple-500 to-pink-500 text-white shadow ring-1 ring-indigo-400/30">
                        <i data-feather="inbox" class="w-5 h-5"></i>
                    </span>
                    Inbox Disposisi
                </h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola disposisi surat masuk yang ditugaskan kepada Anda</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="$dispatch('refresh-dispositions')"
                    class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
            </div>
        </div>

        <!-- Dynamic Table -->
        <div x-data="dispositionsInbox()" x-init="fetch()" @refresh-dispositions.window="fetch()">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Disposisi</p>
                            <p class="text-2xl font-bold text-gray-800 dark:text-gray-100 mt-1" x-text="stats.total"></p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <i data-feather="inbox" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Belum Dibaca</p>
                            <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1" x-text="stats.unread"></p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-amber-500/10 flex items-center justify-center">
                            <i data-feather="mail" class="w-6 h-6 text-amber-600 dark:text-amber-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Sedang Dikerjakan</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1" x-text="stats.in_progress"></p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-blue-500/10 flex items-center justify-center">
                            <i data-feather="clock" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Selesai</p>
                            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1" x-text="stats.completed"></p>
                        </div>
                        <div class="h-12 w-12 rounded-lg bg-emerald-500/10 flex items-center justify-center">
                            <i data-feather="check-circle" class="w-6 h-6 text-emerald-600 dark:text-emerald-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
                <div class="grid gap-4 md:gap-6 md:grid-cols-5 items-end text-sm">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-feather="search" class="w-4 h-4 text-gray-400"></i>
                            </span>
                            <input type="text" x-model="filters.q" @input.debounce.500ms="fetch()"
                                placeholder="Nomor / Perihal / Instruksi"
                                class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
                        <select x-model="filters.status" @change="fetch()"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
                            <option value="">Semua</option>
                            <option value="pending">Menunggu</option>
                            <option value="in_progress">Sedang Dikerjakan</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
                        <select x-model="filters.priority" @change="fetch()"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
                            <option value="">Semua</option>
                            <option value="normal">Normal</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" x-model="filters.unread" @change="fetch()"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-xs text-gray-600 dark:text-gray-300">Belum Dibaca</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nomor Surat</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perihal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dari</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Instruksi</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prioritas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Batas Waktu</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <template x-if="loading">
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="flex items-center justify-center gap-2 text-gray-500 dark:text-gray-400">
                                            <span class="animate-spin h-5 w-5 rounded-full border-2 border-indigo-600 border-t-transparent"></span>
                                            <span>Memuat data...</span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="!loading && !data.length">
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <i data-feather="inbox" class="w-12 h-12 mx-auto mb-3 opacity-50"></i>
                                        <p>Tidak ada disposisi ditemukan</p>
                                    </td>
                                </tr>
                            </template>
                            <template x-for="row in data" :key="row.id">
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors"
                                    :class="{ 'bg-indigo-50/30 dark:bg-indigo-900/10': !row.read_at }">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <span x-show="!row.read_at" class="h-2 w-2 rounded-full bg-indigo-600 animate-pulse"></span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="row.letter_number"></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100 max-w-xs truncate" x-text="row.subject"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600 dark:text-gray-300" x-text="row.from_user"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-xs text-gray-600 dark:text-gray-400 max-w-xs truncate" x-text="row.instruction"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': row.priority === 'low',
                                                'bg-slate-500/10 text-slate-600 dark:text-slate-300': row.priority === 'normal',
                                                'bg-amber-500/10 text-amber-600 dark:text-amber-400': row.priority === 'high',
                                                'bg-rose-500/10 text-rose-600 dark:text-rose-400': row.priority === 'urgent'
                                            }"
                                            x-text="row.priority === 'urgent' ? 'Urgent' : row.priority === 'high' ? 'Tinggi' : row.priority === 'low' ? 'Rendah' : 'Normal'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full"
                                            :class="{
                                                'bg-amber-500/10 text-amber-600 dark:text-amber-400': row.status === 'pending',
                                                'bg-blue-500/10 text-blue-600 dark:text-blue-400': row.status === 'in_progress',
                                                'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': row.status === 'completed'
                                            }"
                                            x-text="row.status === 'pending' ? 'Menunggu' : row.status === 'in_progress' ? 'Dikerjakan' : 'Selesai'">
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs" :class="row.is_overdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-gray-600 dark:text-gray-400'">
                                            <span x-text="row.due_date || '-'"></span>
                                            <span x-show="row.is_overdue" class="ml-1">⚠️</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <button @click="openDetail(row)"
                                            class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm font-medium">
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 dark:bg-gray-700/40 px-4 py-3 border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <button @click="prevPage()" :disabled="meta.current_page <= 1"
                                class="btn-sm bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 disabled:opacity-50">
                                Previous
                            </button>
                            <button @click="nextPage()" :disabled="meta.current_page >= meta.last_page"
                                class="btn-sm bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 disabled:opacity-50">
                                Next
                            </button>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                    Menampilkan <span class="font-medium" x-text="((meta.current_page - 1) * meta.per_page) + 1"></span>
                                    sampai <span class="font-medium" x-text="Math.min(meta.current_page * meta.per_page, meta.total)"></span>
                                    dari <span class="font-medium" x-text="meta.total"></span> hasil
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                    <button @click="prevPage()" :disabled="meta.current_page <= 1"
                                        class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                        <i data-feather="chevron-left" class="w-4 h-4"></i>
                                    </button>
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        <span x-text="meta.current_page"></span> / <span x-text="meta.last_page"></span>
                                    </span>
                                    <button @click="nextPage()" :disabled="meta.current_page >= meta.last_page"
                                        class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm font-medium text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                        <i data-feather="chevron-right" class="w-4 h-4"></i>
                                    </button>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        @include('pages.unit_kerja.inbox-disposisi.detail.detail-modal')
        @include('pages.unit_kerja.inbox-disposisi.detail.response-modal')
    </div>

    <script>
        function dispositionsInbox() {
            return {
                loading: false,
                data: [],
                meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
                stats: { total: 0, unread: 0, in_progress: 0, completed: 0 },
                filters: {
                    q: '',
                    status: '',
                    priority: '',
                    unread: false,
                    page: 1
                },
                async fetch() {
                    this.loading = true;
                    try {
                        const params = new URLSearchParams();
                        Object.keys(this.filters).forEach(k => {
                            if (this.filters[k]) params.append(k, this.filters[k]);
                        });
                        const res = await fetch(`/unit-kerja/api/dispositions/inbox?${params}`, {
                            headers: { 'Accept': 'application/json' }
                        });
                        if (res.ok) {
                            const json = await res.json();
                            this.data = json.data;
                            this.meta = json.meta;
                            this.calculateStats();
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => { if (window.feather) feather.replace(); });
                    }
                },
                calculateStats() {
                    // Simple client-side stats from fetched data
                    this.stats.total = this.meta.total;
                    this.stats.unread = this.data.filter(d => !d.read_at).length;
                    this.stats.in_progress = this.data.filter(d => d.status === 'in_progress').length;
                    this.stats.completed = this.data.filter(d => d.status === 'completed').length;
                },
                prevPage() {
                    if (this.filters.page > 1) {
                        this.filters.page--;
                        this.fetch();
                    }
                },
                nextPage() {
                    if (this.filters.page < this.meta.last_page) {
                        this.filters.page++;
                        this.fetch();
                    }
                },
                openDetail(row) {
                    this.$dispatch('open-modal', { modal: 'showDetail', row: row });
                }
            }
        }
    </script>
</x-app-layout>
