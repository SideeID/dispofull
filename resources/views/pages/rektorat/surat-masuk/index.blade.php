<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto" x-data="{
        showView: false,
        showDisposition: false,
        showAttachment: false,
        showSign: false,
        showHistory: false,
        showPreview: false,
        selected: null,
        open(modal, row = null) {
            // Force re-mount so inner x-data gets fresh selected and init() re-runs
            this[modal] = false;
            this.selected = row;
            this.$nextTick(() => { this[modal] = true; });
        },
        closeAll() { this.showView = false;
            this.showDisposition = false;
            this.showAttachment = false;
            this.showSign = false;
            this.showHistory = false;
            this.showPreview = false; },
    }"
    @keydown.escape.window="closeAll()"
    @open-modal="open($event.detail.modal, $event.detail.row)"
    @open-modal.window="open($event.detail.modal, $event.detail.row)">
        @php
            $priorityColors = [
                'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
                'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
            ];
            $statusColors = [
                'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                'in_progress' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
                'processed' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                'review' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
            ];
        @endphp

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
                        <i data-feather="inbox" class="w-5 h-5"></i>
                    </span>
                    Surat Masuk
                </h1>
            </div>
            <div class="flex items-center gap-3">
                <button @click="$dispatch('refresh-incoming-letters')"
                    class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </button>
                <button
                    class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
                    <i data-feather="download" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Export</span>
                </button>
            </div>
        </div>

        <!-- Filter + Table (Dynamic) -->
        <div x-data="incomingLetters()" x-init="fetch()">
        <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
            <div class="grid gap-4 md:gap-6 md:grid-cols-6 items-end text-sm">
                <div class="md:col-span-2">
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-feather="search" class="w-4 h-4 text-gray-400"></i>
                        </span>
                        <input type="text" x-model="filters.q" placeholder="Nomor / Perihal / Pengirim"
                            class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Dari</label>
                    <input type="date" x-model="filters.date_from"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal
                        Sampai</label>
                    <input type="date" x-model="filters.date_to"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
                    <select x-model="filters.status"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
                        <option value="">Semua</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="processed">Processed</option>
                        <option value="review">Review</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
                    <select x-model="filters.priority"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
                        <option value="">Semua</option>
                        <option value="low">Low</option>
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="flex gap-2 md:col-span-1">
                    <button type="button" @click="applyFilter()"
                        class="flex-1 bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium rounded-lg px-4 py-2 transition">Terapkan</button>
                    <button type="button" @click="resetFilters()"
                        class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-xs text-center">Reset</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
            <div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i
                        data-feather="list" class="w-4 h-4 text-amber-500"></i> Daftar Surat Masuk</h2>
                <div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
                    <span class="flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Nomor</th>
                            <th class="text-left px-5 py-3 font-semibold">Perihal</th>
                            <th class="text-left px-5 py-3 font-semibold">Pengirim</th>
                            <th class="text-left px-5 py-3 font-semibold">Tanggal</th>
                            <th class="text-left px-5 py-3 font-semibold">Prioritas</th>
                            <th class="text-left px-5 py-3 font-semibold">Status</th>
                            <th class="text-left px-5 py-3 font-semibold">Lampiran</th>
                            <th class="text-left px-5 py-3 font-semibold">Disposisi</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                        <template x-for="row in letters" :key="row.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
                                <td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-200">
                                    <div class="flex items-center gap-2">
                                        <span x-text="row.number"></span>
                                        <span x-show="row.agenda" class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">AG</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300 max-w-[260px]">
                                    <span class="line-clamp-1" x-text="row.subject"></span>
                                </td>
                                <td class="px-5 py-3 text-gray-600 dark:text-gray-300" x-text="row.from"></td>
                                <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs" x-text="row.date"></td>
                                <td class="px-5 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-medium" x-text="row.priority"></span>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-medium" x-text="row.status"></span>
                                </td>
                                <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300" x-text="row.attachments"></td>
                                <td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300" x-text="row.dispositions"></td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="$dispatch('open-modal', { modal: 'showView', row })" class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">Detail</button>
                                        <button @click="$dispatch('open-modal', { modal: 'showDisposition', row })" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">Disposisi</button>
                                        <button @click="$dispatch('open-modal', { modal: 'showAttachment', row })" class="text-slate-600 dark:text-slate-300 hover:underline text-xs font-medium">Lampiran</button>
                                        <button x-show="row.letter_type?.code === 'ST'" @click="archiveLetter(row)" class="text-green-600 dark:text-green-400 hover:underline text-xs font-medium">Arsipkan</button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!letters.length"><td colspan="9" class="px-5 py-6 text-center text-[12px] text-gray-400">Tidak ada data</td></tr>
                    </tbody>
                </table>
            </div>

            <div
                class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
                <span>Menampilkan <span x-text="letters.length"></span> surat</span>
                <div class="flex gap-1" x-show="meta.last_page > 1">
                    <button @click="changePage(meta.current_page-1)" :disabled="meta.current_page === 1"
                        class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed"><i
                            data-feather="chevron-left" class="w-3 h-3"></i></button>
                    <template x-for="p in meta.last_page" :key="p">
                        <button @click="changePage(p)" class="px-2 py-1 rounded"
                            :class="p === meta.current_page ? 'bg-amber-600 text-white' :
                                'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600'"
                            x-text="p"></button>
                    </template>
                    <button @click="changePage(meta.current_page+1)" :disabled="meta.current_page === meta.last_page"
                        class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed"><i
                            data-feather="chevron-right" class="w-3 h-3"></i></button>
                </div>
            </div>
        </div>

        </div><!-- end dynamic wrapper -->

        @include('pages.rektorat.surat-masuk.detail.view-modal')
        @include('pages.rektorat.surat-masuk.detail.disposition-modal')
        @include('pages.rektorat.surat-masuk.detail.attachment-modal')
        @include('pages.rektorat.surat-masuk.detail.sign-modal')
        @include('pages.rektorat.surat-masuk.detail.history-modal')
        @include('pages.rektorat.surat-masuk.detail.preview-modal')

        <div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat ·
            Universitas Bakrie</div>

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('incomingLetters', () => ({
                    letters: [],
                    loading: false,
                    meta: { current_page: 1, last_page: 1, per_page: 15, total: 0 },
                    filters: { q: '', status: '', priority: '', date_from: '', date_to: '' },
                    init() {
                        window.addEventListener('refresh-incoming-letters', () => this.fetch());
                    },
                    debounceTimer: null,
                    changePage(p) { if (p < 1 || p > this.meta.last_page) return; this.meta.current_page = p; this.fetch(); },
                    applyFilter() { this.meta.current_page = 1; this.fetch(); },
                    resetFilters() {
                        this.filters = { q: '', status: '', priority: '', date_from: '', date_to: '' };
                        this.meta.current_page = 1;
                        this.fetch();
                    },
                    async fetch() {
                        this.loading = true;
                        try {
                            const params = new URLSearchParams({ page: this.meta.current_page });
                            Object.entries(this.filters).forEach(([k,v])=>{ if (typeof v === 'boolean') { if (v) params.set(k, '1'); } else if (v) params.set(k, v); });
                            const res = await fetch(`/rektor/api/incoming-letters?${params.toString()}`, { headers: { 'Accept': 'application/json' } });
                            if (!res.ok) throw new Error('Fetch failed');
                            const json = await res.json();
                            this.letters = json.data;
                            this.meta = json.meta;
                            this.$nextTick(() => { if (window.feather) feather.replace(); });
                        } catch (e) { console.error(e); }
                        finally { this.loading = false; }
                    },
                    async archiveLetter(letter) {
                        if (!confirm(`Arsipkan surat "${letter.subject}"?\n\nSurat yang diarsipkan akan dipindahkan ke halaman arsip.`)) return;
                        try {
                            console.log('Archiving letter:', letter.id);
                            const res = await fetch(`/rektor/api/incoming-letters/${letter.id}/archive`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            });
                            console.log('Response status:', res.status);
                            const data = await res.json();
                            console.log('Response data:', data);
                            if (!res.ok) {
                                alert(data.message || 'Gagal mengarsipkan surat. Silakan coba lagi.');
                                return;
                            }
                            alert('Surat berhasil diarsipkan!');
                            await this.fetch(); // Refresh list
                        } catch (e) {
                            console.error('Archive error:', e);
                            alert('Gagal mengarsipkan surat. Silakan coba lagi.');
                        }
                    }
                }));
            });
        </script>
    </div>
</x-app-layout>
