<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">
        {{-- <div class="sm:flex sm:justify-between sm:items-center mb-8">
            <div class="mb-4 sm:mb-0">
                <h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold flex items-center gap-3">
                    <span
                        class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow">
                        <i data-feather="file-text" class="w-5 h-5"></i>
                    </span>
                    Dashboard Sistem Surat
                </h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ringkasan aktivitas & performa pengelolaan surat
                    kampus</p>
            </div>
            <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
                <x-datepicker />
                <a href="#"
                    class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
                    <i data-feather="file-plus" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Buat Surat</span>
                </a>
                <a href="#"
                    class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
                    <i data-feather="refresh-cw" class="w-4 h-4"></i>
                    <span class="hidden sm:inline">Refresh</span>
                </a>
            </div>
        </div> --}}

        <div class="grid grid-cols-12 gap-6 mb-10">
            @php
                $cards = [
                    [
                        'label' => 'Surat Masuk (Hari ini)',
                        'value' => 18,
                        'icon' => 'inbox',
                        'color' => 'from-amber-500 to-orange-500',
                        'delta' => '+12%',
                    ],
                    [
                        'label' => 'Surat Keluar (Draft)',
                        'value' => 7,
                        'icon' => 'edit',
                        'color' => 'from-blue-500 to-indigo-500',
                        'delta' => '2 baru',
                    ],
                    [
                        'label' => 'Disposisi Pending',
                        'value' => 9,
                        'icon' => 'git-branch',
                        'color' => 'from-rose-500 to-pink-500',
                        'delta' => '3 urgent',
                    ],
                    [
                        'label' => 'Butuh Tanda Tangan',
                        'value' => 5,
                        'icon' => 'pen-tool',
                        'color' => 'from-teal-500 to-emerald-500',
                        'delta' => '1 prioritas',
                    ],
                ];
            @endphp
            @foreach ($cards as $c)
                <div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
                    <div
                        class="relative overflow-hidden rounded-2xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4">
                        <div class="flex items-start justify-between">
                            <div class="text-xs font-medium tracking-wide text-gray-500 dark:text-gray-400 uppercase">
                                {{ $c['label'] }}</div>
                            <div class="rounded-xl p-2 bg-gradient-to-tr {{ $c['color'] }} text-white shadow">
                                <i data-feather="{{ $c['icon'] }}" class="w-4 h-4"></i>
                            </div>
                        </div>
                        <div class="flex items-end justify-between">
                            <div class="text-3xl font-semibold text-gray-800 dark:text-gray-100">{{ $c['value'] }}
                            </div>
                            <div
                                class="text-[11px] px-2 py-1 rounded-full bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-300 font-medium">
                                {{ $c['delta'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-12 gap-8">
            <div class="col-span-12 lg:col-span-7 xl:col-span-8 space-y-6">
                <div
                    class="rounded-2xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
                    <div
                        class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2"><i
                                data-feather="inbox" class="w-4 h-4 text-amber-500"></i> Surat Masuk Terbaru</h2>
                        <a href="#"
                            class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">Lihat
                            Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700/40 text-gray-600 dark:text-gray-300">
                                <tr>
                                    <th class="text-left px-6 py-2 font-medium">No Surat</th>
                                    <th class="text-left px-6 py-2 font-medium">Perihal</th>
                                    <th class="text-left px-6 py-2 font-medium">Dari</th>
                                    <th class="text-left px-6 py-2 font-medium">Tanggal</th>
                                    <th class="text-left px-6 py-2 font-medium">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                @php
                                    $incoming = [
                                        [
                                            'no' => '012/SM/REKT/09/2025',
                                            'sub' => 'Undangan Rapat Koordinasi',
                                            'from' => 'Kementerian DIKTI',
                                            'date' => '26 Sep 2025',
                                            'status' => 'Baru',
                                        ],
                                        [
                                            'no' => '013/SM/BAA/09/2025',
                                            'sub' => 'Permohonan Data Akademik',
                                            'from' => 'LLDIKTI III',
                                            'date' => '26 Sep 2025',
                                            'status' => 'Diproses',
                                        ],
                                        [
                                            'no' => '014/SM/WR2/09/2025',
                                            'sub' => 'Surat Pemberitahuan Audit',
                                            'from' => 'Akuntan Publik XYZ',
                                            'date' => '25 Sep 2025',
                                            'status' => 'Disposisi',
                                        ],
                                        [
                                            'no' => '015/SM/P3M/09/2025',
                                            'sub' => 'Kerjasama Penelitian',
                                            'from' => 'PT Teknologi Nusantara',
                                            'date' => '25 Sep 2025',
                                            'status' => 'Arsip',
                                        ],
                                    ];
                                @endphp
                                @foreach ($incoming as $row)
                                    <tr class="hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-6 py-2 font-mono text-xs text-gray-500 dark:text-gray-400">
                                            {{ $row['no'] }}</td>
                                        <td class="px-6 py-2 text-gray-800 dark:text-gray-100 font-medium">
                                            {{ $row['sub'] }}</td>
                                        <td class="px-6 py-2 text-gray-600 dark:text-gray-300">{{ $row['from'] }}</td>
                                        <td class="px-6 py-2 text-gray-500 dark:text-gray-400">{{ $row['date'] }}</td>
                                        <td class="px-6 py-2">
                                            @php
                                                $badgeColors = [
                                                    'Baru' =>
                                                        'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300',
                                                    'Diproses' =>
                                                        'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                                    'Disposisi' =>
                                                        'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                                    'Arsip' =>
                                                        'bg-gray-200 text-gray-700 dark:bg-gray-600/40 dark:text-gray-300',
                                                ];
                                            @endphp
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $badgeColors[$row['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
                    <div
                        class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
                        <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2"><i
                                data-feather="git-branch" class="w-4 h-4 text-rose-500"></i> Disposisi Terbaru</h2>
                        <a href="#"
                            class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">Lihat
                            Semua</a>
                    </div>
                    <ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
                        @php
                            $disposisi = [
                                [
                                    'instruksi' => 'Segera telaah & laporkan hasil verifikasi dokumen',
                                    'surat' => '012/SM/REKT/09/2025',
                                    'to' => 'WR I',
                                    'priority' => 'Tinggi',
                                    'status' => 'Pending',
                                ],
                                [
                                    'instruksi' => 'Koordinasikan penyusunan draft tanggapan',
                                    'surat' => '014/SM/WR2/09/2025',
                                    'to' => 'BAA',
                                    'priority' => 'Normal',
                                    'status' => 'Proses',
                                ],
                                [
                                    'instruksi' => 'Siapkan bahan presentasi rapat',
                                    'surat' => '011/SM/REKT/09/2025',
                                    'to' => 'BTI',
                                    'priority' => 'Tinggi',
                                    'status' => 'Selesai',
                                ],
                            ];
                            $priorityColor = [
                                'Tinggi' => 'text-rose-600 dark:text-rose-400',
                                'Normal' => 'text-amber-600 dark:text-amber-400',
                            ];
                            $statusColor = [
                                'Pending' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                'Proses' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300',
                                'Selesai' =>
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                            ];
                        @endphp
                        @foreach ($disposisi as $d)
                            <li class="px-6 py-4 hover:bg-gray-50/70 dark:hover:bg-gray-700/30 transition">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="mt-1 w-8 h-8 rounded-xl bg-gradient-to-tr from-rose-500 to-pink-500 text-white flex items-center justify-center text-[11px] font-semibold shadow">
                                        DP</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-gray-800 dark:text-gray-100 font-medium line-clamp-1">
                                            {{ $d['instruksi'] }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Surat: <span
                                                class="font-mono">{{ $d['surat'] }}</span> • Ke: <span
                                                class="font-medium">{{ $d['to'] }}</span></p>
                                        <div class="mt-2 flex items-center gap-2">
                                            <span
                                                class="text-[11px] font-medium {{ $priorityColor[$d['priority']] ?? 'text-gray-500' }}">Prioritas:
                                                {{ $d['priority'] }}</span>
                                            <span
                                                class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColor[$d['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $d['status'] }}</span>
                                        </div>
                                    </div>
                                    <button class="text-gray-400 hover:text-amber-600"><i data-feather="more-vertical"
                                            class="w-4 h-4"></i></button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-5 xl:col-span-4 space-y-6">
                <div class="rounded-2xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4"><i
                            data-feather="zap" class="w-4 h-4 text-amber-500"></i> Aksi Cepat</h2>
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        @php
                            $actions = [
                                [
                                    'label' => 'Input Surat Masuk',
                                    'icon' => 'inbox',
                                    'color' => 'from-amber-500 to-orange-500',
                                ],
                                [
                                    'label' => 'Buat Surat Keluar',
                                    'icon' => 'file-plus',
                                    'color' => 'from-blue-500 to-indigo-500',
                                ],
                                [
                                    'label' => 'Buat Surat Tugas',
                                    'icon' => 'file-text',
                                    'color' => 'from-purple-500 to-fuchsia-500',
                                ],
                                [
                                    'label' => 'Agenda Surat',
                                    'icon' => 'calendar',
                                    'color' => 'from-lime-500 to-green-500',
                                ],
                                ['label' => 'Nomor Surat', 'icon' => 'hash', 'color' => 'from-gray-500 to-gray-600'],
                                [
                                    'label' => 'Upload Lampiran',
                                    'icon' => 'paperclip',
                                    'color' => 'from-teal-500 to-emerald-500',
                                ],
                            ];
                        @endphp
                        @foreach ($actions as $a)
                            <a href="#"
                                class="group rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-800 hover:bg-gradient-to-tr {{ $a['color'] }} hover:text-white p-3 flex flex-col gap-3 transition relative overflow-hidden">
                                <div class="flex items-center justify-between">
                                    <div
                                        class="rounded-lg p-2 bg-gradient-to-tr {{ $a['color'] }} text-white shadow">
                                        <i data-feather="{{ $a['icon'] }}" class="w-4 h-4"></i>
                                    </div>
                                    <i data-feather="chevron-right"
                                        class="w-3 h-3 text-gray-400 group-hover:text-white"></i>
                                </div>
                                <span
                                    class="text-[11px] font-medium text-gray-600 dark:text-gray-300 group-hover:text-white leading-tight">{{ $a['label'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5">
                    <h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4"><i
                            data-feather="pen-tool" class="w-4 h-4 text-teal-500"></i> Tanda Tangan Digital</h2>
                    <ul class="space-y-4 text-sm">
                        @php
                            $sign = [
                                [
                                    'no' => 'ST/045/WR1/09/2025',
                                    'judul' => 'Surat Tugas Evaluasi Kurikulum',
                                    'status' => 'Menunggu Anda',
                                ],
                                [
                                    'no' => 'ST/041/REKT/09/2025',
                                    'judul' => 'Surat Tugas Audit Internal',
                                    'status' => 'Diverifikasi',
                                ],
                                [
                                    'no' => 'ST/039/WR2/09/2025',
                                    'judul' => 'Surat Tugas Penyusunan Laporan',
                                    'status' => 'Menunggu Pihak Lain',
                                ],
                            ];
                            $statusMap = [
                                'Menunggu Anda' => 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300',
                                'Diverifikasi' =>
                                    'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300',
                                'Menunggu Pihak Lain' =>
                                    'bg-gray-200 text-gray-700 dark:bg-gray-600/40 dark:text-gray-300',
                            ];
                        @endphp
                        @foreach ($sign as $s)
                            <li class="group">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="w-8 h-8 rounded-xl bg-gradient-to-tr from-teal-500 to-emerald-500 text-white flex items-center justify-center text-[10px] font-semibold shadow">
                                        TT</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-mono text-gray-500 dark:text-gray-400">
                                            {{ $s['no'] }}</p>
                                        <p class="text-gray-800 dark:text-gray-100 font-medium line-clamp-1">
                                            {{ $s['judul'] }}</p>
                                        <span
                                            class="inline-block mt-1 px-2 py-0.5 rounded-full text-[10px] font-medium {{ $statusMap[$s['status']] ?? 'bg-gray-100 text-gray-600' }}">{{ $s['status'] }}</span>
                                    </div>
                                    <button class="text-gray-400 hover:text-amber-600"><i data-feather="arrow-right"
                                            class="w-4 h-4"></i></button>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="mt-4 text-right">
                        <a href="#"
                            class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">Lihat
                            Semua</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">
            Sistem Pengelolaan Surat · Universitas Bakrie · Versi Beta Internal
        </div>
    </div>
</x-app-layout>
