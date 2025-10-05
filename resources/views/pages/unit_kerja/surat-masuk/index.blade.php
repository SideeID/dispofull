<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="suratMasukUnitKerja()"
		 x-init="init()"
		 @keydown.escape.window="closeAll()"
	>
		@php
			// Placeholder data surat masuk untuk unit kerja
			// (Nanti diganti query: IncomingLetter::forUnit(auth()->user()->unit_id)->latest()->paginate())
			$incoming = [
				['number'=>'SM-102/UK/2025','subject'=>'Permintaan Data Inventaris','from'=>'BAA','date'=>'2025-10-02','priority'=>'normal','status'=>'pending','attachments'=>1,'dispositions'=>0,'category'=>'Internal'],
				['number'=>'SM-099/EXT/2025','subject'=>'Undangan Workshop Digitalisasi','from'=>'PT Teknologi Nusantara','date'=>'2025-10-02','priority'=>'high','status'=>'review','attachments'=>2,'dispositions'=>1,'category'=>'Eksternal'],
				['number'=>'SM-095/UK/2025','subject'=>'Rencana Audit Internal','from'=>'SPI','date'=>'2025-10-01','priority'=>'urgent','status'=>'in_progress','attachments'=>0,'dispositions'=>2,'category'=>'Internal'],
				['number'=>'SM-091/EXT/2025','subject'=>'Penawaran Kerja Sama','from'=>'CV Solusi Media','date'=>'2025-09-30','priority'=>'low','status'=>'processed','attachments'=>3,'dispositions'=>1,'category'=>'Eksternal'],
				['number'=>'SM-087/UK/2025','subject'=>'Notulensi Rapat Koordinasi','from'=>'Sekretariat','date'=>'2025-09-29','priority'=>'normal','status'=>'processed','attachments'=>1,'dispositions'=>3,'category'=>'Internal'],
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
			];
		@endphp

		<!-- Header -->
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-indigo-600 to-violet-500 text-white shadow ring-1 ring-indigo-400/40">
						<i data-feather="inbox" class="w-5 h-5"></i>
					</span>
					Surat Masuk
				</h1>
				<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Daftar surat masuk yang ditujukan ke unit kerja Anda</p>
			</div>
			<div class="flex items-center gap-3">
				<button @click="refresh()" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 text-xs font-medium px-4 py-2">
					<i data-feather="refresh-cw" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Refresh</span>
				</button>
				<button @click="open('showExport')" class="btn bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium px-4 py-2 flex items-center gap-2">
					<i data-feather="download" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Export</span>
				</button>
			</div>
		</div>

		<!-- Filters -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 p-5 mb-6">
			<form method="GET" action="#" class="grid gap-4 md:gap-6 md:grid-cols-7 items-end text-sm">
				<div class="md:col-span-2">
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Pencarian</label>
					<div class="relative">
						<i data-feather="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
						<input type="text" name="q" placeholder="Nomor / Perihal / Pengirim" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Dari</label>
					<input type="date" name="date_from" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Sampai</label>
					<input type="date" name="date_to" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="pending">Pending</option>
						<option value="in_progress">In Progress</option>
						<option value="processed">Processed</option>
						<option value="review">Review</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select name="priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="low">Low</option>
						<option value="normal">Normal</option>
						<option value="high">High</option>
						<option value="urgent">Urgent</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kategori</label>
					<select name="category" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="Internal">Internal</option>
						<option value="Eksternal">Eksternal</option>
					</select>
				</div>
				<div class="flex gap-2 md:col-span-1">
					<button class="flex-1 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-medium rounded-lg px-4 py-2 transition">Filter</button>
					<a href="#" class="flex-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg px-4 py-2 text-xs text-center">Reset</a>
				</div>
			</form>
		</div>

		<!-- Table -->
		<div class="bg-white dark:bg-gray-800 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
			<div class="px-5 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-indigo-500"></i> Daftar Surat Masuk</h2>
				<div class="flex items-center gap-2 text-[11px] text-gray-500 dark:text-gray-400">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
				</div>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr class="text-[11px] uppercase tracking-wide">
							<th class="px-5 py-3 text-left font-semibold">Nomor</th>
							<th class="px-5 py-3 text-left font-semibold">Perihal</th>
							<th class="px-5 py-3 text-left font-semibold">Pengirim</th>
							<th class="px-5 py-3 text-left font-semibold">Tgl Surat</th>
							<th class="px-5 py-3 text-left font-semibold">Kategori</th>
							<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
							<th class="px-5 py-3 text-left font-semibold">Status</th>
							<th class="px-5 py-3 text-center font-semibold">Disposisi</th>
							<th class="px-5 py-3 text-center font-semibold">Lampiran</th>
							<th class="px-5 py-3 text-center font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@foreach($incoming as $s)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40" x-data="{ row: @js($s) }">
								<td class="px-5 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">{{ $s['number'] }}</td>
								<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
									<div class="font-medium flex items-center gap-2">
										<span class="w-1.5 h-1.5 rounded-full {{ $s['priority']=='urgent' ? 'bg-rose-500' : ($s['priority']=='high' ? 'bg-amber-500' : ($s['priority']=='normal' ? 'bg-slate-400' : 'bg-emerald-500')) }}"></span>
										{{ Str::limit($s['subject'],42) }}
									</div>
								</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $s['from'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $s['date'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $s['category'] }}</td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $priorityColors[$s['priority']] ?? '' }}">{{ $s['priority'] }}</span></td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColors[$s['status']] ?? '' }}">{{ $s['status'] }}</span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $s['dispositions'] }}</td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $s['attachments'] }}</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-center gap-1">
										<button @click="open('showView', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Detail"><i data-feather="eye" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showAttachment', row)" x-show="row.attachments>0" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Lampiran"><i data-feather="paperclip" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showHistory', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Riwayat"><i data-feather="clock" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showDisposition', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Usul Disposisi" x-show="canSuggestDisposition(row)"><i data-feather="git-branch" class="w-3.5 h-3.5"></i></button>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ count($incoming) }} dari {{ count($incoming) }} surat</span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded bg-indigo-600 text-white">1</button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.unit_kerja.surat-masuk.detail.view-modal')
		@include('pages.unit_kerja.surat-masuk.detail.attachment-modal')
		@include('pages.unit_kerja.surat-masuk.detail.history-modal')
		@include('pages.unit_kerja.surat-masuk.detail.disposition-suggest-modal')
		@include('pages.unit_kerja.surat-masuk.detail.export-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>

	<script>
		function suratMasukUnitKerja(){
			return {
				showView:false, showAttachment:false, showHistory:false, showDisposition:false, showExport:false,
				selected:null,
				open(modal,row=null){ this.selected=row; this[modal]=true; },
				closeAll(){ this.showView=false; this.showAttachment=false; this.showHistory=false; this.showDisposition=false; this.showExport=false; },
				canSuggestDisposition(row){ return row && (row.status==='pending' || row.status==='review'); },
				refresh(){ /* placeholder fetch */ alert('Refresh (dummy)'); },
				init(){ /* future: load filters from query */ },
				submitSuggestion(){ alert('Usulan disposisi dikirim (dummy)'); this.closeAll(); },
				exportData(){ alert('Export (dummy)'); this.closeAll(); },
			}
		}
	</script>
</x-app-layout>
