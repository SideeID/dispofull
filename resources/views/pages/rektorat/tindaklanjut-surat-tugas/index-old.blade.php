<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showCreate:false, showEdit:false, showView:false, showParticipants:false, showAttachments:false, showSign:false, showPublish:false, showHistory:false,
			selected:null,
			open(modal,row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showCreate=false; this.showEdit=false; this.showView=false; this.showParticipants=false; this.showAttachments=false; this.showSign=false; this.showPublish=false; this.showHistory=false; },
		}"
		@keydown.escape.window="closeAll()"
	>
		@php
			// Placeholder data surat tugas (gabungan draft dan existing)
			$tasks = [
				['number'=>null,'temp'=>'DRAFT-001','subject'=>'Draft Supervisi Proyek A','destination'=>'Tim Proyek A','date'=>'2025-10-02','start'=>'2025-10-07','end'=>'2025-10-10','priority'=>'high','status'=>'draft','participants'=>2,'files'=>1],
				['number'=>'ST-052/REK/2025','subject'=>'Monitoring Penelitian Hibah','destination'=>'Direktorat Riset','date'=>'2025-10-01','start'=>'2025-10-10','end'=>'2025-10-12','priority'=>'normal','status'=>'need_signature','participants'=>5,'files'=>1],
				['number'=>'ST-047/REK/2025','subject'=>'Rapat Koordinasi Akreditasi','destination'=>'Sekretariat','date'=>'2025-09-30','start'=>'2025-10-05','end'=>'2025-10-05','priority'=>'urgent','status'=>'signed','participants'=>6,'files'=>3],
				['number'=>'ST-044/REK/2025','subject'=>'Kunjungan Mitra Luar Negeri','destination'=>'Kantor Kerjasama','date'=>'2025-09-29','start'=>'2025-10-15','end'=>'2025-10-18','priority'=>'high','status'=>'published','participants'=>3,'files'=>0],
				['number'=>'ST-039/REK/2025','subject'=>'Evaluasi Kurikulum Semester','destination'=>'WR I','date'=>'2025-09-28','start'=>'2025-10-20','end'=>'2025-10-21','priority'=>'low','status'=>'archived','participants'=>8,'files'=>2],
			];
			$priorityColors = [
				'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
			];
			$statusColors = [
				'draft' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'need_signature' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
				'signed' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
				'published' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'archived' => 'bg-slate-500/10 text-slate-600 dark:text-slate-400',
			];
		@endphp

		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-emerald-400/30">
						<i data-feather="edit" class="w-6 h-6"></i>
					</span>
					Buat / Tindaklanjuti Surat Tugas
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="open('showCreate')" class="btn bg-emerald-600 hover:bg-emerald-500 text-white border-0 shadow-sm flex items-center gap-2">
					<i data-feather="plus" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Draft Baru</span>
				</button>
				<button @click="$dispatch('refresh-task-letters')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
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
						<input type="text" name="q" placeholder="No / Perihal / Tujuan" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" name="date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="draft">Draft</option>
						<option value="need_signature">Perlu TTD</option>
						<option value="signed">Signed</option>
						<option value="published">Published</option>
						<option value="archived">Arsip</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select name="priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="urgent">Urgent</option>
						<option value="high">High</option>
						<option value="normal">Normal</option>
						<option value="low">Low</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
					<input type="date" name="start_from" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
					<input type="date" name="end_to" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100" />
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
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-emerald-500"></i> Surat Tugas (Draft & Proses)</h2>
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
							<th class="px-5 py-3 text-left font-semibold">No / Draft</th>
							<th class="px-5 py-3 text-left font-semibold">Perihal</th>
							<th class="px-5 py-3 text-left font-semibold">Tujuan / Tim</th>
							<th class="px-5 py-3 text-left font-semibold">Periode</th>
							<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
							<th class="px-5 py-3 text-left font-semibold">Status</th>
							<th class="px-5 py-3 text-center font-semibold">Peserta</th>
							<th class="px-5 py-3 text-center font-semibold">File</th>
							<th class="px-5 py-3 text-center font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@foreach($tasks as $t)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40" x-data="{ row: @js($t) }">
								<td class="px-5 py-3 font-mono text-xs text-emerald-600 dark:text-emerald-400">
									@if($t['number']) {{ $t['number'] }} @else <span class="italic text-slate-400">{{ $t['temp'] }}</span> @endif
								</td>
								<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
									<div class="font-medium flex items-center gap-2">
										<span class="w-1.5 h-1.5 rounded-full {{ $t['priority']=='urgent' ? 'bg-rose-500' : ($t['priority']=='high' ? 'bg-amber-500' : ($t['priority']=='normal' ? 'bg-slate-400' : 'bg-emerald-500')) }}"></span>
										{{ Str::limit($t['subject'],40) }}
									</div>
									<div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Tgl: {{ $t['date'] }}</div>
								</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $t['destination'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $t['start'] }} s/d {{ $t['end'] }}</td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $priorityColors[$t['priority']] ?? '' }}">{{ $t['priority'] }}</span></td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColors[$t['status']] ?? '' }}">{{ $t['status'] }}</span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $t['participants'] }}</td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $t['files'] }}</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-center gap-1">
										<button @click="open('showView', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 dark:hover:bg-gray-600" title="Detail"><i data-feather="eye" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showParticipants', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 dark:hover:bg-gray-600" title="Peserta"><i data-feather="users" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showAttachments', row)" x-show="row.files>0" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 dark:hover:bg-gray-600" title="Lampiran"><i data-feather="paperclip" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showSign', row)" x-show="['draft','need_signature'].includes(row.status)" class="p-1.5 rounded bg-emerald-600 text-white hover:bg-emerald-500" title="Tanda Tangan"><i data-feather="pen-tool" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showPublish', row)" x-show="['signed'].includes(row.status)" class="p-1.5 rounded bg-indigo-600 text-white hover:bg-indigo-500" title="Publish"><i data-feather="share-2" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showEdit', row)" x-show="row.status=='draft'" class="p-1.5 rounded bg-amber-600 text-white hover:bg-amber-500" title="Edit"><i data-feather="edit-2" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showHistory', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-emerald-100 dark:hover:bg-gray-600" title="Riwayat"><i data-feather="clock" class="w-3.5 h-3.5"></i></button>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ count($tasks) }} dari {{ count($tasks) }} surat tugas</span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded bg-emerald-600 text-white">1</button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.create-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.edit-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.view-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.participants-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.attachment-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.sign-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.publish-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.history-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
