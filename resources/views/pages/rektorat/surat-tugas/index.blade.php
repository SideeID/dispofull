<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showView:false, showCreate:false, showParticipants:false, showSign:false, showHistory:false,
			selected:null,
			open(modal,row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showView=false; this.showCreate=false; this.showParticipants=false; this.showSign=false; this.showHistory=false; },
		 }"
		 @keydown.escape.window="closeAll()"
	>
		@php
			// Placeholder data surat tugas - nanti ganti query (Letter::where('letter_type_id', X)->whereDirection('outgoing')->latest()->paginate())
			$assignments = [
				['number'=>'ST-044/REK/2025','subject'=>'Monitoring Kegiatan KKN Wilayah Timur','destination'=>'Tim Monitoring KKN','date'=>'2025-10-02','start'=>'2025-10-05','end'=>'2025-10-12','priority'=>'high','status'=>'draft','participants'=>5,'files'=>1],
				['number'=>'ST-037/REK/2025','subject'=>'Kunjungan Kerja ke Kampus Mitra','destination'=>'WR II & Tim Kerjasama','date'=>'2025-10-01','start'=>'2025-10-08','end'=>'2025-10-09','priority'=>'normal','status'=>'signed','participants'=>3,'files'=>2],
				['number'=>'ST-029/REK/2025','subject'=>'Pendampingan Akreditasi Prodi','destination'=>'Tim Akreditasi','date'=>'2025-09-30','start'=>'2025-10-03','end'=>'2025-10-07','priority'=>'urgent','status'=>'need_signature','participants'=>4,'files'=>3],
				['number'=>'ST-021/REK/2025','subject'=>'Audit Internal Sistem Informasi','destination'=>'BTI & Auditor Internal','date'=>'2025-09-29','start'=>'2025-10-10','end'=>'2025-10-14','priority'=>'normal','status'=>'published','participants'=>6,'files'=>0],
				['number'=>'ST-018/REK/2025','subject'=>'Pelatihan Kurikulum Merdeka','destination'=>'WR I & Tim Kurikulum','date'=>'2025-09-28','start'=>'2025-10-15','end'=>'2025-10-16','priority'=>'low','status'=>'archived','participants'=>8,'files'=>2],
			];
			$priorityColors = [
				'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
			];
			$statusColors = [
				'draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'need_signature' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'signed' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
				'published' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'archived' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400',
			];
		@endphp

		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
						<i data-feather="briefcase" class="w-5 h-5"></i>
					</span>
					Surat Tugas
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="open('showCreate')" class="btn bg-amber-600 hover:bg-amber-500 text-white border-0 shadow-sm flex items-center gap-2">
					<i data-feather="plus" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Buat Surat Tugas</span>
				</button>
				<button @click="$dispatch('refresh-assignment-letters')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
					<i data-feather="refresh-cw" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Refresh</span>
				</button>
			</div>
		</div>

		<!-- Filter -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
			<form method="GET" action="#" class="grid gap-4 md:gap-6 md:grid-cols-7 items-end text-sm">
				<div class="md:col-span-2">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
					<div class="relative">
						<span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
							<i data-feather="search" class="w-4 h-4 text-gray-400"></i>
						</span>
						<input type="text" name="q" placeholder="Nomor / Perihal / Tujuan" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" name="date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
					<input type="date" name="start_from" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
					<input type="date" name="end_to" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="draft">Draft</option>
						<option value="need_signature">Butuh TTD</option>
						<option value="signed">Sudah TTD</option>
						<option value="published">Dipublikasikan</option>
						<option value="archived">Arsip</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select name="priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="low">Low</option>
						<option value="normal">Normal</option>
						<option value="high">High</option>
						<option value="urgent">Urgent</option>
					</select>
				</div>
				<div class="flex gap-2 md:col-span-1">
					<button class="flex-1 bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium rounded-lg px-4 py-2 transition">Filter</button>
					<a href="#" class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-xs text-center">Reset</a>
				</div>
			</form>
		</div>

		<!-- Table -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
			<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-amber-500"></i> Daftar Surat Tugas</h2>
				<div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
				</div>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr>
							<th class="text-left px-5 py-3 font-semibold">Nomor</th>
							<th class="text-left px-5 py-3 font-semibold">Perihal</th>
							<th class="text-left px-5 py-3 font-semibold">Tujuan / Tim</th>
							<th class="text-left px-5 py-3 font-semibold">Tanggal Surat</th>
							<th class="text-left px-5 py-3 font-semibold">Periode</th>
							<th class="text-left px-5 py-3 font-semibold">Prioritas</th>
							<th class="text-left px-5 py-3 font-semibold">Status</th>
							<th class="text-left px-5 py-3 font-semibold">Peserta</th>
							<th class="text-left px-5 py-3 font-semibold">File</th>
							<th class="px-5 py-3"></th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@foreach($assignments as $row)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
								<td class="px-5 py-3 font-medium text-gray-700 dark:text-gray-200">{{ $row['number'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300 max-w-[260px]"><span class="line-clamp-1">{{ $row['subject'] }}</span></td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $row['destination'] }}</td>
								<td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300 text-xs">{{ \Carbon\Carbon::parse($row['start'])->format('d M') }} - {{ \Carbon\Carbon::parse($row['end'])->format('d M') }}</td>
								<td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $priorityColors[$row['priority']] }}">{{ ucfirst($row['priority']) }}</span></td>
								<td class="px-5 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $statusColors[$row['status']] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-300' }}">{{ str_replace('_',' ', $row['status']) }}</span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['participants'] }}</td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['files'] }}</td>
								<td class="px-5 py-3 text-right">
									<div class="flex items-center justify-end gap-2">
										<button @click="open('showView', {{ json_encode($row) }})" class="text-amber-600 dark:text-amber-400 hover:underline text-xs font-medium">Detail</button>
										<button @click="open('showParticipants', {{ json_encode($row) }})" class="text-indigo-600 dark:text-indigo-400 hover:underline text-xs font-medium">Peserta</button>
										@if(in_array($row['status'],['need_signature','draft']))
											<button @click="open('showSign', {{ json_encode($row) }})" class="text-emerald-600 dark:text-emerald-400 hover:underline text-xs font-medium">TTD</button>
										@endif
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ count($assignments) }} dari {{ count($assignments) }} surat tugas</span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded bg-amber-600 text-white">1</button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.rektorat.surat-tugas.detail.view-modal')
		@include('pages.rektorat.surat-tugas.detail.create-modal')
		@include('pages.rektorat.surat-tugas.detail.participants-modal')
		@include('pages.rektorat.surat-tugas.detail.sign-modal')
		@include('pages.rektorat.surat-tugas.detail.history-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
