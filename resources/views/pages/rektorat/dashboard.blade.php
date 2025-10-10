<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

		@php
			// Placeholder data untuk role Rektorat (ganti dengan query Eloquent nantinya)
			$stats = [
				[
					'label' => 'Surat Masuk (Bulan Ini)',
					'value' => 128,
					'icon' => 'inbox',
					'delta' => '+8%',
					'delta_type' => 'up'
				],
				[
					'label' => 'Surat Keluar (Bulan Ini)',
					'value' => 54,
					'icon' => 'send',
					'delta' => '+3%',
					'delta_type' => 'up'
				],
				[
					'label' => 'Disposisi Pending',
					'value' => 7,
					'icon' => 'clipboard',
					'delta' => '2 terlambat',
					'delta_type' => 'warn'
				],
				[
					'label' => 'Menunggu Tanda Tangan',
					'value' => 5,
					'icon' => 'pen-tool',
					'delta' => '3 prioritas',
					'delta_type' => 'priority'
				],
			];

			$recentIncoming = [
				[
					'number' => 'SM-001/REK/2025',
					'subject' => 'Permohonan Kerja Sama Penelitian',
					'from' => 'Kementerian Pendidikan',
					'date' => '2025-10-02',
					'priority' => 'high',
					'status' => 'pending'
				],
				[
					'number' => 'SM-014/P3M/2025',
					'subject' => 'Undangan Seminar Nasional',
					'from' => 'Universitas Negeri A',
					'date' => '2025-10-01',
					'priority' => 'normal',
					'status' => 'in_progress'
				],
				[
					'number' => 'SM-017/WR1/2025',
					'subject' => 'Laporan Triwulan Penelitian',
					'from' => 'Wakil Rektor I',
					'date' => '2025-09-30',
					'priority' => 'normal',
					'status' => 'review'
				],
				[
					'number' => 'SM-021/BAA/2025',
					'subject' => 'Rekap Yudisium Program Studi',
					'from' => 'BAA',
					'date' => '2025-09-29',
					'priority' => 'low',
					'status' => 'processed'
				],
			];

			$signatureQueue = [
				[
					'number' => 'SK-023/REK/2025',
					'type' => 'SK',
					'subject' => 'SK Pengangkatan Panitia PKKMB',
					'requested_at' => '2025-10-02 09:15',
					'priority' => 'urgent'
				],
				[
					'number' => 'ST-044/REK/2025',
					'type' => 'ST',
					'subject' => 'Surat Tugas Monitoring KKN',
					'requested_at' => '2025-10-02 08:40',
					'priority' => 'high'
				],
				[
					'number' => 'SE-011/REK/2025',
					'type' => 'SE',
					'subject' => 'Surat Edaran Jam Kerja Libur Nasional',
					'requested_at' => '2025-10-01 16:10',
					'priority' => 'normal'
				],
			];

			$pendingDispositions = [
				[
					'letter' => 'SM-001/REK/2025',
					'to' => 'WR I',
					'instruction' => 'Kajian akademik & rekomendasi',
					'due' => '2025-10-05',
					'status' => 'in_progress'
				],
				[
					'letter' => 'SM-017/WR1/2025',
					'to' => 'P3M',
					'instruction' => 'Verifikasi data riset',
					'due' => '2025-10-07',
					'status' => 'pending'
				],
				[
					'letter' => 'SM-021/BAA/2025',
					'to' => 'BAA',
					'instruction' => 'Konfirmasi data kelulusan',
					'due' => '2025-10-04',
					'status' => 'pending'
				],
			];

			$agendas = [
				[
					'title' => 'Agenda Surat Keluar Oktober',
					'period' => '01-31 Okt 2025',
					'type' => 'monthly',
					'letters' => 54,
					'status' => 'draft'
				],
				[
					'title' => 'Agenda Surat Masuk Minggu 40',
					'period' => '29 Sep - 05 Okt',
					'type' => 'weekly',
					'letters' => 42,
					'status' => 'generated'
				],
				[
					'title' => 'Agenda Disposisi Prioritas',
					'period' => 'Sep 2025',
					'type' => 'custom',
					'letters' => 18,
					'status' => 'archived'
				],
			];

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
				'draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'generated' => 'bg-teal-500/10 text-teal-600 dark:text-teal-400',
				'archived' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400',
			];
		@endphp

		<div class="mb-8">
			<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
				<div>
					<h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold flex items-center gap-2">
						<i data-feather="shield" class="w-6 h-6 text-amber-500"></i>
						Dashboard Rektorat
					</h1>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ringkasan strategis aktivitas surat dan tindak lanjut</p>
				</div>
				<div class="flex items-center gap-2">
					<button class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 text-sm">
						<i data-feather="refresh-cw" class="w-4 h-4"></i> Refresh
					</button>
					<button class="btn bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-2 text-sm">
						<i data-feather="file-plus" class="w-4 h-4"></i> Buat Surat
					</button>
				</div>
			</div>
		</div>

		<!-- Kartu Statistik -->
		<div class="grid grid-cols-12 gap-6 mb-10">
			@foreach($stats as $s)
				<div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
					<div class="rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4 h-full">
						<div class="flex items-center justify-between">
							<div class="h-10 w-10 flex items-center justify-center rounded-lg bg-gradient-to-tr from-amber-500 to-yellow-400 text-white shadow text-[13px]">
								<i data-feather="{{ $s['icon'] }}" class="w-5 h-5"></i>
							</div>
							@php
								$deltaColor = match($s['delta_type']) {
									'up' => 'text-emerald-600 dark:text-emerald-400',
									'warn' => 'text-amber-600 dark:text-amber-400',
									'priority' => 'text-rose-600 dark:text-rose-400',
									default => 'text-slate-500 dark:text-slate-400'
								};
							@endphp
							<div class="text-[11px] font-medium {{ $deltaColor }}">{{ $s['delta'] }}</div>
						</div>
						<div>
							<div class="text-2xl font-bold text-gray-800 dark:text-gray-100">{{ $s['value'] }}</div>
							<div class="text-[11px] tracking-wide uppercase font-semibold text-gray-500 dark:text-gray-400 mt-1">{{ $s['label'] }}</div>
						</div>
					</div>
				</div>
			@endforeach
		</div>

		<div class="grid grid-cols-12 gap-8">
			<!-- Kolom utama -->
			<div class="col-span-12 lg:col-span-8 space-y-8">

				<!-- Surat Masuk Terbaru -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="inbox" class="w-4 h-4 text-amber-500"></i> Surat Masuk Terbaru
						</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Lihat Semua</a>
					</div>
					<div class="overflow-x-auto">
						<table class="min-w-full text-sm">
							<thead class="bg-gray-50 dark:bg-gray-700/40 text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400">
								<tr>
									<th class="text-left px-5 py-3 font-semibold">Nomor</th>
									<th class="text-left px-5 py-3 font-semibold">Perihal</th>
									<th class="text-left px-5 py-3 font-semibold">Dari</th>
									<th class="text-left px-5 py-3 font-semibold">Tanggal</th>
									<th class="text-left px-5 py-3 font-semibold">Prioritas</th>
									<th class="text-left px-5 py-3 font-semibold">Status</th>
									<th class="px-5 py-3"></th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
								@foreach($recentIncoming as $r)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
										<td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-200">{{ $r['number'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $r['subject'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $r['from'] }}</td>
										<td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
										<td class="px-5 py-3">
											<span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $priorityColors[$r['priority']] }}">{{ ucfirst($r['priority']) }}</span>
										</td>
										<td class="px-5 py-3">
											<span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $statusColors[$r['status']] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-300' }}">{{ str_replace('_',' ',$r['status']) }}</span>
										</td>
										<td class="px-5 py-3 text-right">
											<button class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">Detail</button>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<!-- Disposisi Pending / Sedang Berjalan -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="git-merge" class="w-4 h-4 text-amber-500"></i> Disposisi Aktif
						</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Kelola</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@foreach($pendingDispositions as $d)
							<li class="px-5 py-3 flex items-center gap-4">
								<div class="flex-1 min-w-0">
									<div class="flex items-center gap-2">
										<span class="font-medium text-gray-700 dark:text-gray-200 text-xs">{{ $d['letter'] }}</span>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusColors[$d['status']] ?? '' }}">{{ str_replace('_',' ',$d['status']) }}</span>
									</div>
									<div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-1">Instruksi: {{ $d['instruction'] }}</div>
								</div>
								<div class="text-right">
									<div class="text-[11px] font-medium text-gray-600 dark:text-gray-300">Ke: {{ $d['to'] }}</div>
									<div class="text-[10px] text-amber-600 dark:text-amber-400 mt-0.5">Jatuh tempo {{ \Carbon\Carbon::parse($d['due'])->format('d M') }}</div>
								</div>
							</li>
						@endforeach
					</ul>
				</div>

			</div>

			<!-- Sidebar kanan -->
			<div class="col-span-12 lg:col-span-4 space-y-8">

				<!-- Antrean Tanda Tangan -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="pen-tool" class="w-4 h-4 text-amber-500"></i> Antrean Tanda Tangan</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Buka</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@foreach($signatureQueue as $q)
							<li class="px-5 py-3 flex items-start gap-3">
								<div class="mt-0.5">
									<span class="px-2 py-1 rounded-md text-[10px] font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400">{{ $q['type'] }}</span>
								</div>
								<div class="flex-1 min-w-0">
									<div class="flex items-center gap-2">
										<span class="font-medium text-gray-700 dark:text-gray-200 text-xs">{{ $q['number'] }}</span>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $priorityColors[$q['priority']] }}">{{ ucfirst($q['priority']) }}</span>
									</div>
									<div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $q['subject'] }}</div>
									<div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Diminta {{ \Carbon\Carbon::parse($q['requested_at'])->diffForHumans() }}</div>
								</div>
								<div class="flex flex-col gap-1">
									<button class="text-[10px] px-2 py-1 rounded bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-semibold">Tandatangani</button>
									<button class="text-[10px] px-2 py-1 rounded bg-slate-500/10 text-slate-600 dark:text-slate-300 font-medium">Detail</button>
								</div>
							</li>
						@endforeach
					</ul>
				</div>

				<!-- Agenda Surat -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="calendar" class="w-4 h-4 text-amber-500"></i> Agenda Surat</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Kelola</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@foreach($agendas as $a)
							<li class="px-5 py-3">
								<div class="flex items-start justify-between gap-3">
									<div class="flex-1 min-w-0">
										<div class="font-medium text-gray-700 dark:text-gray-200 text-xs">{{ $a['title'] }}</div>
										<div class="text-[11px] text-gray-500 dark:text-gray-400 mt-1">Periode: {{ $a['period'] }}</div>
										<div class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">{{ $a['letters'] }} surat tercakup</div>
									</div>
									<div class="text-right space-y-1">
										<span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $statusColors[$a['status']] ?? '' }}">{{ ucfirst($a['status']) }}</span>
										<div class="text-[10px] text-slate-500 dark:text-slate-400">{{ ucfirst($a['type']) }}</div>
									</div>
								</div>
							</li>
						@endforeach
					</ul>
				</div>

				<!-- Aksi Cepat -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4 text-sm"><i data-feather="zap" class="w-4 h-4 text-amber-500"></i> Aksi Cepat</h2>
					<div class="grid grid-cols-2 gap-3 text-[11px] font-medium">
						<a href="#" class="flex flex-col items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40 hover:bg-amber-50 dark:hover:bg-gray-700 transition group">
							<span class="h-8 w-8 rounded-md bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white shadow text-[11px]"><i data-feather="pen-tool" class="w-4 h-4"></i></span>
							<span class="text-gray-600 dark:text-gray-300 text-center leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400">Tanda Tangani<br>Surat</span>
						</a>
						<a href="#" class="flex flex-col items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40 hover:bg-amber-50 dark:hover:bg-gray-700 transition group">
							<span class="h-8 w-8 rounded-md bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white shadow text-[11px]"><i data-feather="git-merge" class="w-4 h-4"></i></span>
							<span class="text-gray-600 dark:text-gray-300 text-center leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400">Buat<br>Disposisi</span>
						</a>
						<a href="#" class="flex flex-col items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40 hover:bg-amber-50 dark:hover:bg-gray-700 transition group">
							<span class="h-8 w-8 rounded-md bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white shadow text-[11px]"><i data-feather="file-plus" class="w-4 h-4"></i></span>
							<span class="text-gray-600 dark:text-gray-300 text-center leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400">Buat<br>Surat Keluar</span>
						</a>
						<a href="#" class="flex flex-col items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40 hover:bg-amber-50 dark:hover:bg-gray-700 transition group">
							<span class="h-8 w-8 rounded-md bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white shadow text-[11px]"><i data-feather="calendar" class="w-4 h-4"></i></span>
							<span class="text-gray-600 dark:text-gray-300 text-center leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400">Susun<br>Agenda</span>
						</a>
						<a href="#" class="flex flex-col items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40 hover:bg-amber-50 dark:hover:bg-gray-700 transition group">
							<span class="h-8 w-8 rounded-md bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white shadow text-[11px]"><i data-feather="download" class="w-4 h-4"></i></span>
							<span class="text-gray-600 dark:text-gray-300 text-center leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400">Export<br>Laporan</span>
						</a>
						<a href="#" class="flex flex-col items-center gap-2 p-3 rounded-lg bg-gray-50 dark:bg-gray-700/40 hover:bg-amber-50 dark:hover:bg-gray-700 transition group">
							<span class="h-8 w-8 rounded-md bg-gradient-to-tr from-amber-500 to-yellow-400 flex items-center justify-center text-white shadow text-[11px]"><i data-feather="search" class="w-4 h-4"></i></span>
							<span class="text-gray-600 dark:text-gray-300 text-center leading-tight group-hover:text-amber-600 dark:group-hover:text-amber-400">Cari<br>Surat</span>
						</a>
					</div>
				</div>

			</div>
		</div>

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">
			Sistem Pengelolaan Surat · Universitas Bakrie · Dashboard Rektorat
		</div>

	</div>
</x-app-layout>
