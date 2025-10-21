<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showView:false, showHistory:false, showParticipants:false, showAttachment:false, showExport:false,
			selected:null,
			open(modal,row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showView=false; this.showHistory=false; this.showParticipants=false; this.showAttachment=false; this.showExport=false; },
		 }"
		 @keydown.escape.window="closeAll()"
	>
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-slate-400/30">
						<i data-feather="archive" class="w-5 h-5"></i>
					</span>
					Arsip Surat Tugas
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="open('showExport')" class="btn bg-yellow-600 hover:bg-yellow-500 text-white flex items-center gap-2">
					<i data-feather="download" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Export</span>
				</button>
				<button @click="$dispatch('open-archive-modal')" class="btn bg-orange-600 hover:bg-orange-500 text-white flex items-center gap-2">
					<i data-feather="archive" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Arsipkan</span>
				</button>
				{{-- <form method="GET" action="{{ route('unit_kerja.archives.export') }}">
					<input type="hidden" name="q" value="{{ request('q') }}" />
					<input type="hidden" name="date" value="{{ request('date') }}" />
					<input type="hidden" name="priority" value="{{ request('priority') }}" />
					<input type="hidden" name="start_from" value="{{ request('start_from') }}" />
					<input type="hidden" name="end_to" value="{{ request('end_to') }}" />
					<button type="submit" class="btn bg-slate-700 hover:bg-slate-600 text-white border-0 shadow-sm flex items-center gap-2">
						<i data-feather="download" class="w-4 h-4"></i>
						<span class="hidden sm:inline">Export</span>
					</button>
				</form> --}}
			</div>
		</div>

		<!-- Filter -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
			<form method="GET" action="{{ route('unit_kerja.arsip.surat.tugas') }}" class="grid gap-4 md:gap-6 md:grid-cols-7 items-end text-sm">
				<div class="md:col-span-2">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
					<div class="relative">
						<input type="text" name="q" value="{{ request('q') }}" placeholder="Nomor / perihal / tujuan" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 pl-9 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
						<i data-feather="search" class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select name="priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100">
						@php $selp = request('priority'); @endphp
						<option value="" @selected($selp==='')>Semua</option>
						<option value="low" @selected($selp==='low')>Low</option>
						<option value="normal" @selected($selp==='normal')>Normal</option>
						<option value="high" @selected($selp==='high')>High</option>
						<option value="urgent" @selected($selp==='urgent')>Urgent</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
					<input type="date" name="start_from" value="{{ request('start_from') }}" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
					<input type="date" name="end_to" value="{{ request('end_to') }}" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div class="flex gap-2 md:col-span-1">
					<button class="flex-1 bg-amber-600 hover:bg-amber-500 text-white text-xs font-medium rounded-lg px-4 py-2 transition">Filter</button>
					<a href="{{ route('unit_kerja.arsip.surat.tugas') }}" class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-xs text-center">Reset</a>
				</div>
			</form>
		</div>

		<!-- Table -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
			<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="archive" class="w-4 h-4 text-slate-500"></i> Daftar Arsip</h2>
				<div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
				</div>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr class="text-xs uppercase tracking-wide">
							<th class="px-5 py-3 text-left font-semibold">Nomor</th>
							<th class="px-5 py-3 text-left font-semibold">Perihal</th>
							<th class="px-5 py-3 text-left font-semibold">Tujuan</th>
							<th class="px-5 py-3 text-left font-semibold">Periode</th>
							<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
							<th class="px-5 py-3 text-left font-semibold">Lampiran</th>
							<th class="px-5 py-3 text-right font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@foreach($archives as $a)
						<tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40" x-data="{ row: @js($a) }">
							<td class="px-5 py-3 font-mono text-[11px] text-gray-500 dark:text-gray-400 align-top">
								<div class="flex flex-col gap-1">
									<span>{{ $a['number'] }}</span>
									<button @click="open('showHistory', row)" class="text-[10px] text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-300">Riwayat</button>
								</div>
							</td>
							<td class="px-5 py-3 text-gray-700 dark:text-gray-200 align-top">
								<div class="font-medium">{{ $a['subject'] }}</div>
								<div class="text-[11px] text-gray-400 dark:text-gray-500">{{ $a['date'] }}</div>
							</td>
							<td class="px-5 py-3 text-gray-600 dark:text-gray-300 align-top">{{ $a['destination'] }}</td>
							<td class="px-5 py-3 text-gray-600 dark:text-gray-300 align-top">
								<div>{{ $a['start'] }} s/d {{ $a['end'] }}</div>
								@php
									$dur = null;
									if(!empty($a['start']) && !empty($a['end'])){
										try { $dur = (new DateTime($a['end']))->diff(new DateTime($a['start']))->days + 1; } catch(Exception $e) { $dur = null; }
									}
								@endphp
								@if($dur)
									<div class="text-[10px] text-gray-400 dark:text-gray-500">{{ $dur }} hari</div>
								@endif
							</td>
							<td class="px-5 py-3 align-top">
								<span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $priorityColors[$a['priority']] ?? 'bg-slate-500/10 text-slate-600 dark:text-slate-300' }}">{{ $a['priority'] }}</span>
							</td>
							<td class="px-5 py-3 text-gray-600 dark:text-gray-300 align-top">{{ $a['files'] }}</td>
							<td class="px-5 py-3 text-right align-top">
								<div class="flex items-center justify-end gap-1">
									<button @click="open('showView', row)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-slate-600 dark:text-slate-300" title="Detail"><i data-feather='eye' class='w-4 h-4'></i></button>
									<button @click="open('showParticipants', row)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-slate-600 dark:text-slate-300" title="Peserta"><i data-feather='users' class='w-4 h-4'></i></button>
									<button @click="open('showAttachment', row)" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-slate-600 dark:text-slate-300" title="Lampiran"><i data-feather='paperclip' class='w-4 h-4'></i></button>
								</div>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ $paginator->total() }} arsip</span>
				<div class="text-xs">{{ $paginator->appends(request()->query())->links() }}</div>
			</div>
		</div>

		@include('pages.unit_kerja.arsip-surat-tugas.detail.view-modal')
		@include('pages.unit_kerja.arsip-surat-tugas.detail.participants-modal')
		@include('pages.unit_kerja.arsip-surat-tugas.detail.attachment-modal')
		@include('pages.unit_kerja.arsip-surat-tugas.detail.history-modal')
		@include('pages.unit_kerja.arsip-surat-tugas.detail.export-modal')

		@include('pages.unit_kerja.arsip-surat-tugas.detail.archive-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
