<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

		@php
			// Placeholder data khusus role unit_kerja (akan diganti query Eloquent)
			$stats = [
				[
					'label' => 'Surat Masuk (Hari Ini)',
					'value' => 12,
					'icon' => 'inbox',
					'delta' => '+3 vs kemarin',
					'delta_type' => 'up',
					'accent' => 'amber'
				],
				[
					'label' => 'Draft Surat Keluar',
					'value' => 5,
					'icon' => 'edit',
					'delta' => '2 baru',
					'delta_type' => 'info',
					'accent' => 'blue'
				],
				[
					'label' => 'Menunggu Tanda Tangan',
					'value' => 2,
					'icon' => 'pen-tool',
					'delta' => '1 prioritas',
					'delta_type' => 'priority',
					'accent' => 'emerald'
				],
				[
					'label' => 'Surat Tugas Diarsipkan (Bulan Ini)',
					'value' => 14,
					'icon' => 'archive',
					'delta' => '+2 minggu ini',
					'delta_type' => 'up',
					'accent' => 'slate'
				],
			];

			$recentIncoming = [
				['number' => 'SM-041/BAA/2025','subject'=>'Undangan Rapat Kurikulum','from'=>'WR I','date'=>'2025-10-05','priority'=>'high','status'=>'pending'],
				['number' => 'SM-039/BAA/2025','subject'=>'Permintaan Data Mahasiswa','from'=>'P3M','date'=>'2025-10-05','priority'=>'normal','status'=>'processed'],
				['number' => 'SM-037/BAA/2025','subject'=>'Notulensi Rapat Akademik','from'=>'Senat','date'=>'2025-10-04','priority'=>'normal','status'=>'review'],
				['number' => 'SM-035/BAA/2025','subject'=>'Pengumuman Jadwal Wisuda','from'=>'Rektorat','date'=>'2025-10-04','priority'=>'urgent','status'=>'pending'],
			];

			$draftOutgoing = [
				['temp'=>'DRAFT-001','subject'=>'Surat Edaran Jadwal Yudisium','type'=>'SE','created_at'=>'2025-10-05 08:40','priority'=>'normal'],
				['temp'=>'DRAFT-002','subject'=>'Permohonan Data Akreditasi','type'=>'ND','created_at'=>'2025-10-05 08:10','priority'=>'high'],
				['temp'=>'DRAFT-003','subject'=>'Undangan Rapat Evaluasi Semester','type'=>'UND','created_at'=>'2025-10-04 16:22','priority'=>'normal'],
				['temp'=>'DRAFT-004','subject'=>'Notulensi Rapat Internal','type'=>'BA','created_at'=>'2025-10-04 15:10','priority'=>'low'],
			];

			$archiveSnapshot = [
				['number'=>'ST-039/REK/2025','subject'=>'Evaluasi Kurikulum Semester','archived_at'=>'2025-10-02','duration'=>2],
				['number'=>'ST-031/REK/2025','subject'=>'Rapat Koordinasi Akreditasi','archived_at'=>'2025-09-30','duration'=>1],
				['number'=>'ST-024/REK/2025','subject'=>'Monitoring Penelitian Hibah','archived_at'=>'2025-09-25','duration'=>2],
			];

			$signQueue = [
				['temp'=>'DRAFT-002','subject'=>'Permohonan Data Akreditasi','type'=>'ND','requested_at'=>'2025-10-05 08:55','priority'=>'high'],
				['temp'=>'DRAFT-001','subject'=>'Surat Edaran Jadwal Yudisium','type'=>'SE','requested_at'=>'2025-10-05 08:42','priority'=>'normal'],
			];

			$quickActions = [
				['icon'=>'file-plus','label'=>'Buat Surat','color'=>'amber','action'=>'#'],
				['icon'=>'inbox','label'=>'Surat Masuk','color'=>'blue','action'=>'#'],
				['icon'=>'archive','label'=>'Arsip ST','color'=>'slate','action'=>'#'],
				['icon'=>'hash','label'=>'Penomoran','color'=>'indigo','action'=>'#'],
			];

			$priorityColors = [
				'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
			];
			$statusColors = [
				'pending' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'processed' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'review' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
			];
		@endphp

		<div class="mb-8">
			<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
				<div>
					<h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold flex items-center gap-2">
						<i data-feather="layers" class="w-6 h-6 text-amber-500"></i>
						Dashboard Unit Kerja
					</h1>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ringkasan aktivitas surat & tugas unit kerja Anda</p>
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

		<!-- Statistik -->
		<div class="grid grid-cols-12 gap-6 mb-10">
			@foreach($stats as $s)
				@php $accent = $s['accent']; @endphp
				<div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
					<div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4 h-full">
						<div class="flex items-start justify-between gap-4">
							<div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">{{ $s['label'] }}</div>
							@php
								$iconClasses = [
									'amber' => 'bg-amber-500 ring-amber-400/40',
									'blue' => 'bg-blue-500 ring-blue-400/40',
									'emerald' => 'bg-emerald-500 ring-emerald-400/40',
									'slate' => 'bg-slate-600 ring-slate-500/40',
								];
								$iconBg = $iconClasses[$accent] ?? 'bg-slate-500 ring-slate-400/40';
							@endphp
							<div class="rounded-lg p-2 text-white shadow ring-1 {{ $iconBg }}">
								<i data-feather="{{ $s['icon'] }}" class="w-4 h-4"></i>
							</div>
						</div>
						<div class="flex items-end justify-between">
							<div class="text-3xl font-semibold text-gray-800 dark:text-gray-100">{{ $s['value'] }}</div>
							@php
								$deltaClass = [
									'up' => 'text-emerald-600 dark:text-emerald-400',
									'priority' => 'text-rose-600 dark:text-rose-400',
									'info' => 'text-slate-500 dark:text-slate-400'
								][$s['delta_type']] ?? 'text-slate-500 dark:text-slate-400';
							@endphp
							<div class="text-[11px] font-medium {{ $deltaClass }}">{{ $s['delta'] }}</div>
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
							<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
								<tr class="text-xs uppercase tracking-wide">
									<th class="px-5 py-3 text-left font-semibold">Nomor</th>
									<th class="px-5 py-3 text-left font-semibold">Perihal</th>
									<th class="px-5 py-3 text-left font-semibold">Dari</th>
									<th class="px-5 py-3 text-left font-semibold">Tanggal</th>
									<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
									<th class="px-5 py-3 text-left font-semibold">Status</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
								@foreach($recentIncoming as $r)
									<tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40">
										<td class="px-5 py-3 font-mono text-[11px] text-gray-500 dark:text-gray-400">{{ $r['number'] }}</td>
										<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
											<div class="font-medium line-clamp-1">{{ $r['subject'] }}</div>
										</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ $r['from'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ $r['date'] }}</td>
										<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $priorityColors[$r['priority']] }}">{{ $r['priority'] }}</span></td>
										<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColors[$r['status']] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-300' }}">{{ $r['status'] }}</span></td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				<!-- Draft Surat Keluar -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="file-text" class="w-4 h-4 text-blue-500"></i> Draft Surat Keluar
						</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Kelola Draft</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@foreach($draftOutgoing as $d)
							<li class="px-5 py-3 hover:bg-gray-50/60 dark:hover:bg-gray-700/40 flex items-start gap-4">
								<div class="w-16">
									<div class="text-[11px] font-mono text-gray-500 dark:text-gray-400">{{ $d['temp'] }}</div>
									<div class="text-[10px] text-gray-400 dark:text-gray-500">{{ substr($d['created_at'],11,5) }}</div>
								</div>
								<div class="flex-1">
									<div class="flex items-center gap-2">
										<span class="text-gray-700 dark:text-gray-200 font-medium line-clamp-1">{{ $d['subject'] }}</span>
										<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400">{{ $d['type'] }}</span>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $priorityColors[$d['priority']] }} capitalize">{{ $d['priority'] }}</span>
									</div>
								</div>
								<div class="flex items-center gap-1">
									<button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400" title="Edit"><i data-feather="edit-2" class="w-4 h-4"></i></button>
									<button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-emerald-600 dark:text-emerald-400" title="Ajukan TTD"><i data-feather="pen-tool" class="w-4 h-4"></i></button>
								</div>
							</li>
						@endforeach
					</ul>
				</div>
			</div>

			<!-- Sidebar kanan -->
			<div class="col-span-12 lg:col-span-4 space-y-8">
				<!-- Arsip Snapshot -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="archive" class="w-4 h-4 text-slate-500"></i> Arsip Surat Tugas Terbaru</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Lihat</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@foreach($archiveSnapshot as $a)
							<li class="px-5 py-3 hover:bg-gray-50/60 dark:hover:bg-gray-700/40">
								<div class="font-mono text-[11px] text-gray-500 dark:text-gray-400">{{ $a['number'] }}</div>
								<div class="text-gray-700 dark:text-gray-200 text-xs font-medium line-clamp-1">{{ $a['subject'] }}</div>
								<div class="text-[10px] text-gray-400 dark:text-gray-500">Arsip: {{ $a['archived_at'] }} · Durasi {{ $a['duration'] }}h</div>
							</li>
						@endforeach
					</ul>
				</div>

				<!-- Antrian Tanda Tangan -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="pen-tool" class="w-4 h-4 text-emerald-500"></i> Antrian Tanda Tangan</h2>
						<a href="#" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Kelola</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@forelse($signQueue as $q)
							<li class="px-5 py-3 flex items-start gap-4 hover:bg-gray-50/60 dark:hover:bg-gray-700/40">
								<div class="w-16">
									<div class="text-[11px] font-mono text-gray-500 dark:text-gray-400">{{ $q['temp'] }}</div>
									<div class="text-[10px] text-gray-400 dark:text-gray-500">{{ substr($q['requested_at'],11,5) }}</div>
								</div>
								<div class="flex-1">
									<div class="flex items-center gap-2">
										<span class="text-gray-700 dark:text-gray-200 text-xs font-medium line-clamp-1">{{ $q['subject'] }}</span>
										<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">{{ $q['type'] }}</span>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-medium {{ $priorityColors[$q['priority']] }} capitalize">{{ $q['priority'] }}</span>
									</div>
								</div>
								<button class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-emerald-600 dark:text-emerald-400" title="Lihat"><i data-feather="eye" class="w-4 h-4"></i></button>
							</li>
						@empty
							<li class="px-5 py-6 text-center text-[11px] text-gray-400 dark:text-gray-500">Tidak ada antrean</li>
						@endforelse
					</ul>
				</div>

				<!-- Aksi Cepat -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4 text-sm"><i data-feather="zap" class="w-4 h-4 text-amber-500"></i> Aksi Cepat</h2>
					<div class="grid grid-cols-2 gap-3 text-[11px] font-medium">
						@foreach($quickActions as $qa)
							@php $c = $qa['color']; @endphp
							<a href="{{ $qa['action'] }}" class="group rounded-lg p-3 ring-1 ring-gray-200 dark:ring-gray-700 bg-white dark:bg-gray-800 hover:bg-gradient-to-tr from-{{ $c }}-50 via-{{ $c }}-50/50 to-transparent dark:hover:from-{{ $c }}-500/10 dark:hover:via-{{ $c }}-500/5 transition flex flex-col gap-2">
								<span class="w-8 h-8 rounded-md bg-{{ $c }}-500/10 text-{{ $c }}-600 dark:text-{{ $c }}-400 flex items-center justify-center"><i data-feather="{{ $qa['icon'] }}" class="w-4 h-4"></i></span>
								<span class="text-gray-600 dark:text-gray-300 group-hover:text-gray-800 dark:group-hover:text-gray-100 leading-tight">{{ $qa['label'] }}</span>
							</a>
						@endforeach
					</div>
				</div>
			</div>
		</div>

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie · Dashboard Unit Kerja</div>
	</div>
</x-app-layout>
