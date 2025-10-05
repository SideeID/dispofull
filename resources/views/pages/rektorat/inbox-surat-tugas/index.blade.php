<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showView:false, showParticipants:false, showAttachments:false, showSign:false, showHistory:false, showAcknowledge:false,
			selected:null,
			open(modal,row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showView=false; this.showParticipants=false; this.showAttachments=false; this.showSign=false; this.showHistory=false; this.showAcknowledge=false; },
		 }"
		 @keydown.escape.window="closeAll()"
	>
		@php
			// Placeholder data inbox surat tugas (akan diganti query Eloquent sesuai kebutuhan)
			$inboxAssignments = [
				['number'=>'ST-055/REK/2025','subject'=>'Supervisi Kegiatan Magang','origin'=>'Wakil Rektor I','date'=>'2025-10-02','start'=>'2025-10-07','end'=>'2025-10-08','priority'=>'high','status'=>'pending_ack','participants'=>4,'files'=>2],
				['number'=>'ST-052/REK/2025','subject'=>'Monitoring Penelitian Hibah','origin'=>'Direktorat Riset','date'=>'2025-10-01','start'=>'2025-10-10','end'=>'2025-10-12','priority'=>'normal','status'=>'need_signature','participants'=>5,'files'=>1],
				['number'=>'ST-047/REK/2025','subject'=>'Rapat Koordinasi Akreditasi','origin'=>'Sekretariat','date'=>'2025-09-30','start'=>'2025-10-05','end'=>'2025-10-05','priority'=>'urgent','status'=>'acknowledged','participants'=>6,'files'=>3],
				['number'=>'ST-044/REK/2025','subject'=>'Kunjungan Mitra Luar Negeri','origin'=>'Kantor Kerjasama','date'=>'2025-09-29','start'=>'2025-10-15','end'=>'2025-10-18','priority'=>'high','status'=>'signed','participants'=>3,'files'=>0],
				['number'=>'ST-039/REK/2025','subject'=>'Evaluasi Kurikulum Semester','origin'=>'WR I','date'=>'2025-09-28','start'=>'2025-10-20','end'=>'2025-10-21','priority'=>'low','status'=>'published','participants'=>8,'files'=>1],
			];
			$priorityColors = [
				'low' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
				'normal' => 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
				'high' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'urgent' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
			];
			$statusColors = [
				'pending_ack' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
				'acknowledged' => 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
				'need_signature' => 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
				'signed' => 'bg-blue-500/10 text-blue-600 dark:text-blue-400',
				'published' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
			];
		@endphp

		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-indigo-400/30">
						<i data-feather="inbox" class="w-6 h-6"></i>
					</span>
					Inbox Surat Tugas
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="$dispatch('refresh-inbox-assignment-letters')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
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
						<input type="text" name="q" placeholder="No / Perihal / Asal" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
					<input type="date" name="date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
					<input type="date" name="start_from" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
					<input type="date" name="end_to" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100" />
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
					<select name="status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="pending_ack">Perlu Konfirmasi</option>
						<option value="need_signature">Perlu TTD</option>
						<option value="acknowledged">Sudah Dikonfirmasi</option>
						<option value="signed">Ditandatangani</option>
						<option value="published">Published</option>
					</select>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
					<select name="priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
						<option value="">Semua</option>
						<option value="urgent">Urgent</option>
						<option value="high">High</option>
						<option value="normal">Normal</option>
						<option value="low">Low</option>
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
				<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="list" class="w-4 h-4 text-indigo-500"></i> Daftar Inbox Surat Tugas</h2>
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
							<th class="px-5 py-3 text-left font-semibold">No Surat</th>
							<th class="px-5 py-3 text-left font-semibold">Perihal</th>
							<th class="px-5 py-3 text-left font-semibold">Asal</th>
							<th class="px-5 py-3 text-left font-semibold">Periode</th>
							<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
							<th class="px-5 py-3 text-left font-semibold">Status</th>
							<th class="px-5 py-3 text-center font-semibold">Peserta</th>
							<th class="px-5 py-3 text-center font-semibold">File</th>
							<th class="px-5 py-3 text-center font-semibold">Aksi</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
						@foreach($inboxAssignments as $a)
							<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40" x-data="{ row: @js($a) }">
								<td class="px-5 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">{{ $a['number'] }}</td>
								<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
									<div class="font-medium flex items-center gap-2">
										<span class="w-1.5 h-1.5 rounded-full {{ $a['priority']=='urgent' ? 'bg-rose-500' : ($a['priority']=='high' ? 'bg-amber-500' : ($a['priority']=='normal' ? 'bg-slate-400' : 'bg-emerald-500')) }}"></span>
										{{ Str::limit($a['subject'],40) }}
									</div>
									<div class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">Tgl: {{ $a['date'] }}</div>
								</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $a['origin'] }}</td>
								<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $a['start'] }} s/d {{ $a['end'] }}</td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $priorityColors[$a['priority']] ?? '' }}">{{ $a['priority'] }}</span></td>
								<td class="px-5 py-3"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium {{ $statusColors[$a['status']] ?? '' }}">{{ $a['status'] }}</span></td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $a['participants'] }}</td>
								<td class="px-5 py-3 text-center text-gray-600 dark:text-gray-300">{{ $a['files'] }}</td>
								<td class="px-5 py-3">
									<div class="flex items-center justify-center gap-1">
										<button @click="open('showView', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Detail"><i data-feather="eye" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showParticipants', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Peserta"><i data-feather="users" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showAttachments', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Lampiran" x-show="row.files>0"><i data-feather="paperclip" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showAcknowledge', row)" class="p-1.5 rounded bg-amber-600 text-white hover:bg-amber-500" title="Konfirmasi" x-show="['pending_ack'].includes(row.status)"><i data-feather="check-circle" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showSign', row)" class="p-1.5 rounded bg-emerald-600 text-white hover:bg-emerald-500" title="Tanda Tangan" x-show="['need_signature'].includes(row.status)"><i data-feather="pen-tool" class="w-3.5 h-3.5"></i></button>
										<button @click="open('showHistory', row)" class="p-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-indigo-100 dark:hover:bg-gray-600" title="Riwayat"><i data-feather="clock" class="w-3.5 h-3.5"></i></button>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-[11px] text-gray-500 dark:text-gray-400">
				<span>Menampilkan {{ count($inboxAssignments) }} dari {{ count($inboxAssignments) }} surat tugas</span>
				<div class="flex gap-1">
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-left" class="w-3 h-3"></i></button>
					<button class="px-2 py-1 rounded bg-indigo-600 text-white">1</button>
					<button class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-700 text-gray-400" disabled><i data-feather="chevron-right" class="w-3 h-3"></i></button>
				</div>
			</div>
		</div>

		@include('pages.rektorat.inbox-surat-tugas.detail.view-modal')
		@include('pages.rektorat.inbox-surat-tugas.detail.participants-modal')
		@include('pages.rektorat.inbox-surat-tugas.detail.attachment-modal')
		@include('pages.rektorat.inbox-surat-tugas.detail.sign-modal')
		@include('pages.rektorat.inbox-surat-tugas.detail.history-modal')
		@include('pages.rektorat.inbox-surat-tugas.detail.ack-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
