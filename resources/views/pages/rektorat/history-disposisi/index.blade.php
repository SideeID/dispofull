<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showView:false, showRoute:false, showNotes:false, showAttachments:false, showHistory:false,
			selected:null,
			open(modal,row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showView=false; this.showRoute=false; this.showNotes=false; this.showAttachments=false; this.showHistory=false; },
		 }"
		 @keydown.escape.window="closeAll()"
	>
		@php
			// Placeholder data history disposisi (gantikan dengan query Eloquent nanti)
			$dispositions = [
				['number'=>'SM-231/UM/2025','subject'=>'Undangan Rapat Senat','origin'=>'Sekretariat Senat','date'=>'2025-10-02','to'=>'Rektor','priority'=>'high','status'=>'completed','chain'=>4,'attachments'=>2],
				['number'=>'SM-227/UM/2025','subject'=>'Laporan Audit Internal','origin'=>'SPI','date'=>'2025-10-02','to'=>'Wakil Rektor II','priority'=>'normal','status'=>'forwarded','chain'=>3,'attachments'=>3],
				['number'=>'SM-220/UM/2025','subject'=>'Permohonan Kerjasama Industri','origin'=>'Kantor Kerjasama','date'=>'2025-10-01','to'=>'Rektor','priority'=>'urgent','status'=>'in_progress','chain'=>5,'attachments'=>1],
				['number'=>'SM-214/UM/2025','subject'=>'Permintaan Data Akreditasi','origin'=>'BAN-PT','date'=>'2025-09-30','to'=>'WR I','priority'=>'high','status'=>'completed','chain'=>6,'attachments'=>4],
				['number'=>'SM-208/UM/2025','subject'=>'Permohonan Cuti Akademik','origin'=>'Fakultas Teknik','date'=>'2025-09-29','to'=>'Arsip','priority'=>'low','status'=>'archived','chain'=>2,'attachments'=>0],
			];
			$priorityColors = [
				'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
			];
			$statusColors = [
				'in_progress' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
				'forwarded' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
				'completed' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'archived' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400',
			];
		@endphp

		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-indigo-400/30">
						<i data-feather="git-branch" class="w-6 h-6"></i>
					</span>
					History Disposisi
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="$dispatch('refresh-history-disposisi')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
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
						<i data-feather="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
						<input type="text" name="q" placeholder="No / Perihal / Asal" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" name="date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="in_progress">Dalam Proses</option>
						<option value="forwarded">Diteruskan</option>
						<option value="completed">Selesai</option>
						<option value="archived">Arsip</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select name="priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="urgent">Urgent</option>
						<option value="high">High</option>
						<option value="normal">Normal</option>
						<option value="low">Low</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tujuan Akhir</label>
					<input type="text" name="to" placeholder="Unit / Jabatan" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100" />
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
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-violet-500"></i> Riwayat Disposisi</h2>
				<div class="flex items-center gap-3 text-[11px] text-gray-500 dark:text-gray-400">
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-rose-500"></span>Urgent</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span>High</span>
					<span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span>Normal</span>
				</div>
			</div>
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
						<tr class="text-[11px] uppercase tracking-wide">
							<th class="px-5 py-3 text-left font-semibold">No Surat</th>
							<th class="px-5 py-3 text-left font-semibold">Perihal</th>
							<th class="px-5 py-3 text-left font-semibold">Asal</th>
							<th class="px-5 py-3 text-left font-semibold">Tgl Surat</th>
							<th class="px-5 py-3 text-left font-semibold">Tujuan Akhir</th>
							<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
							<th class="px-5 py-3 text-left font-semibold">Status</th>
							<th class="px-5 py-3 text-center font-semibold">Langkah</th>
							<th class="px-5 py-3 text-center font-semibold">Lampiran</th>
							<th class="px-5 py-3 text-center font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@foreach($dispositions as $d)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40" x-data="{ row: @js($d) }">
								<td class="px-5 py-3 font-mono text-xs text-violet-600 dark:text-violet-400">{{ $d['number'] }}</td>
								<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
									<div class="font-medium flex items-center gap-2">
										<span class="w-1.5 h-1.5 rounded-full {{ $d['priority']=='urgent' ? 'bg-rose-500' : ($d['priority']=='high' ? 'bg-amber-500' : ($d['priority']=='normal' ? 'bg-slate-400' : 'bg-emerald-500')) }}"></span>
										{{ Str::limit($d['subject'],40) }}
									</div>
								</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $d['origin'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $d['date'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $d['to'] }}</td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $priorityColors[$d['priority']] ?? '' }}">{{ $d['priority'] }}</span></td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColors[$d['status']] ?? '' }}">{{ $d['status'] }}</span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $d['chain'] }}</td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $d['attachments'] }}</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-center gap-1">
										<button @click="open('showView', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Detail"><i data-feather="eye" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showRoute', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Alur"><i data-feather="git-commit" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showNotes', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Catatan"><i data-feather="message-circle" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showAttachments', row)" x-show="row.attachments>0" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Lampiran"><i data-feather="paperclip" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showHistory', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-violet-100 dark:hover:bg-gray-600" title="Riwayat"><i data-feather="clock" class="w-3.5 h-3.5"></i></button>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ count($dispositions) }} dari {{ count($dispositions) }} disposisi</span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded bg-violet-600 text-white">1</button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.rektorat.history-disposisi.detail.view-modal')
		@include('pages.rektorat.history-disposisi.detail.route-modal')
		@include('pages.rektorat.history-disposisi.detail.notes-modal')
		@include('pages.rektorat.history-disposisi.detail.attachment-modal')
		@include('pages.rektorat.history-disposisi.detail.history-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
