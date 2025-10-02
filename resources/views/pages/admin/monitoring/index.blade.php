<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="{
			showHealth:false, showQueue:false, showLogs:false, showStorage:false, showNumbering:false,
			selected:null,
			open(modal,row=null){ this.selected=row; this[modal]=true },
			closeAll(){ this.showHealth=false; this.showQueue=false; this.showLogs=false; this.showStorage=false; this.showNumbering=false; },
		 }"
		 @keydown.escape.window="closeAll()"
	>
		<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 mb-8">
			<div>
				<h1 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-3">
					<span class="inline-flex items-center justify-center h-11 w-11 rounded-lg bg-gradient-to-tr from-orange-500 via-amber-500 to-yellow-400 text-white shadow ring-1 ring-orange-400/30">
						<i data-feather="activity" class="w-5 h-5"></i>
					</span>
					Monitoring Sistem
				</h1>
			</div>
			<div class="flex items-center gap-3">
				<button @click="$dispatch('refresh-monitoring')" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2">
					<i data-feather="refresh-cw" class="w-4 h-4"></i>
					<span class="hidden sm:inline">Refresh</span>
				</button>
			</div>
		</div>

		@php
			$metrics = [
				['label'=>'Surat Masuk (24h)','value'=>128,'icon'=>'inbox','accent'=>'amber','delta'=>'+8%'],
				['label'=>'Surat Keluar (24h)','value'=>74,'icon'=>'send','accent'=>'blue','delta'=>'+2%'],
				['label'=>'Disposisi Aktif','value'=>39,'icon'=>'git-branch','accent'=>'rose','delta'=>'5 pending'],
				['label'=>'Tanda Tangan (Hari Ini)','value'=>22,'icon'=>'pen-tool','accent'=>'emerald','delta'=>'3 menunggu'],
			];
		@endphp

		<div class="grid grid-cols-12 gap-4 mb-10">
			@foreach($metrics as $m)
				@php $c = $m['accent']; @endphp
				<div class="col-span-12 sm:col-span-6 md:col-span-3">
					<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-4 flex flex-col">
						<div class="flex items-start justify-between gap-3">
							<div>
								<div class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $m['label'] }}</div>
								<div class="mt-1 text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $m['value'] }}</div>
							</div>
							<span class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-{{ $c }}-500/10 text-{{ $c }}-600 dark:text-{{ $c }}-400">
								<i data-feather="{{ $m['icon'] }}" class="w-5 h-5"></i>
							</span>
						</div>
						@if(!empty($m['delta']))
							<div class="mt-2 text-[11px] font-medium text-{{ $c }}-600 dark:text-{{ $c }}-400">{{ $m['delta'] }}</div>
						@endif
					</div>
				</div>
			@endforeach
		</div>

		<div class="grid grid-cols-12 gap-8">
			<div class="col-span-12 lg:col-span-7 xl:col-span-8 space-y-6">
				{{-- Health Overview --}}
				@php
					$health = [
						['key'=>'database','name'=>'Database','status'=>'ok','latency_ms'=>12,'detail'=>'Koneksi stabil & respons cepat'],
						['key'=>'cache','name'=>'Cache','status'=>'ok','latency_ms'=>3,'detail'=>'Redis terhubung, hit ratio 92%'],
						['key'=>'queue','name'=>'Queue Worker','status'=>'warn','latency_ms'=>0,'detail'=>'1 job tertunda > 5m'],
						['key'=>'mail','name'=>'Mail Transport','status'=>'ok','latency_ms'=>48,'detail'=>'SMTP respons normal'],
						['key'=>'storage','name'=>'Storage Disk','status'=>'ok','latency_ms'=>0,'detail'=>'Pemakaian 63% dari kuota'],
						['key'=>'sign','name'=>'Digital Signature','status'=>'ok','latency_ms'=>110,'detail'=>'Service tanda tangan respons normal'],
					];
				@endphp
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="heart" class="w-4 h-4 text-rose-500"></i> Kesehatan Komponen</h2>
						<button @click="open('showHealth')" class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='maximize-2' class='w-3 h-3'></i> Detail</button>
					</div>
					<div class="overflow-x-auto">
						<table class="w-full text-sm">
							<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
								<tr>
									<th class="text-left px-5 py-3 font-semibold">Komponen</th>
									<th class="text-left px-5 py-3 font-semibold">Status</th>
									<th class="text-left px-5 py-3 font-semibold">Latency</th>
									<th class="text-left px-5 py-3 font-semibold">Catatan</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
								@foreach($health as $h)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition cursor-pointer" @click="open('showHealth', {{ json_encode($h) }})">
										<td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $h['name'] }}</td>
										<td class="px-5 py-3">
											@php $color = $h['status']=='ok' ? 'emerald':'amber'; @endphp
											<span class="px-2 py-0.5 rounded-lg text-[11px] font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-500/20 dark:text-{{ $color }}-300">{{ strtoupper($h['status']) }}</span>
										</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $h['latency_ms'] }} ms</td>
										<td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $h['detail'] }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				{{-- Queue & Jobs --}}
				@php
					$queues = [
						['name'=>'default','pending'=>3,'processing'=>1,'failed'=>0,'oldest_wait_s'=>340],
						['name'=>'emails','pending'=>0,'processing'=>0,'failed'=>1,'oldest_wait_s'=>0],
						['name'=>'signing','pending'=>2,'processing'=>1,'failed'=>0,'oldest_wait_s'=>95],
					];
				@endphp
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="server" class="w-4 h-4 text-indigo-500"></i> Antrian & Jobs</h2>
						<button @click="open('showQueue')" class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='maximize-2' class='w-3 h-3'></i> Detail</button>
					</div>
					<div class="overflow-x-auto">
						<table class="w-full text-sm">
							<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
								<tr>
									<th class="text-left px-5 py-3 font-semibold">Queue</th>
									<th class="text-left px-5 py-3 font-semibold">Pending</th>
									<th class="text-left px-5 py-3 font-semibold">Processing</th>
									<th class="text-left px-5 py-3 font-semibold">Failed</th>
									<th class="text-left px-5 py-3 font-semibold">Oldest Wait</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
								@foreach($queues as $q)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition">
										<td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $q['name'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $q['pending'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $q['processing'] }}</td>
										<td class="px-5 py-3">@if($q['failed']>0)<span class="px-2 py-0.5 rounded-lg text-[11px] font-medium bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300">{{ $q['failed'] }} gagal</span>@else <span class="text-gray-400 text-xs">0</span> @endif</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $q['oldest_wait_s'] }} s</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>

				{{-- Letter Numbering Usage --}}
				@php
					$numbering = [
						['code'=>'UND','name'=>'Undangan','current'=>182,'reset'=>'YEARLY','pattern'=>'{SEQ}/UND/BKR/{ROMAN_MONTH}/{YEAR}','last_issued'=>'2025-10-02 09:12'],
						['code'=>'SK','name'=>'Surat Keputusan','current'=>44,'reset'=>'YEARLY','pattern'=>'{SEQ}/SK/UB/{YEAR}','last_issued'=>'2025-10-02 08:40'],
						['code'=>'INT','name'=>'Internal Memo','current'=>12,'reset'=>'MONTHLY','pattern'=>'{SEQ}/INT/{MONTH}/{YEAR}','last_issued'=>'2025-10-01 16:05'],
					];
				@endphp
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="px-6 py-4 flex items-center justify-between border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm"><i data-feather="hash" class="w-4 h-4 text-amber-500"></i> Pemakaian Penomoran</h2>
						<button @click="open('showNumbering')" class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 flex items-center gap-1"><i data-feather='maximize-2' class='w-3 h-3'></i> Detail</button>
					</div>
					<div class="overflow-x-auto">
						<table class="w-full text-sm">
							<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
								<tr>
									<th class="text-left px-5 py-3 font-semibold">Kode</th>
									<th class="text-left px-5 py-3 font-semibold">Nama</th>
									<th class="text-left px-5 py-3 font-semibold">Current</th>
									<th class="text-left px-5 py-3 font-semibold">Reset</th>
									<th class="text-left px-5 py-3 font-semibold">Terakhir</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
								@foreach($numbering as $n)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40 transition" @click="open('showNumbering', {{ json_encode($n) }})">
										<td class="px-5 py-3 font-mono text-xs text-gray-700 dark:text-gray-200">{{ $n['code'] }}</td>
										<td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $n['name'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $n['current'] }}</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300">{{ $n['reset'] }}</td>
										<td class="px-5 py-3 text-[11px] text-gray-500 dark:text-gray-400">{{ $n['last_issued'] }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="col-span-12 lg:col-span-5 xl:col-span-4 space-y-6">
				{{-- Storage & Signatures --}}
				@php
					$storage = [
						['name'=>'Surat Masuk','used'=>3.2,'limit'=>10],
						['name'=>'Surat Keluar','used'=>1.8,'limit'=>10],
						['name'=>'Lampiran','used'=>6.5,'limit'=>15],
						['name'=>'Tanda Tangan','used'=>0.4,'limit'=>2],
					];
					$errors = [
						['time'=>'09:21','level'=>'WARNING','message'=>'Queue delay > 300s (default)'],
						['time'=>'08:55','level'=>'ERROR','message'=>'Gagal kirim email notifikasi disposisi'],
						['time'=>'08:10','level'=>'INFO','message'=>'Regenerasi nomor surat (cron) selesai'],
					];
				@endphp
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4 text-sm"><i data-feather="database" class="w-4 h-4 text-indigo-500"></i> Penggunaan Storage</h2>
					<ul class="space-y-4 text-sm">
						@foreach($storage as $s)
							@php $percent = round(($s['used']/$s['limit'])*100); $bar = $percent>80 ? 'bg-rose-500':'bg-amber-500'; @endphp
							<li>
								<div class="flex items-center justify-between mb-1">
									<span class="font-medium text-gray-700 dark:text-gray-200">{{ $s['name'] }}</span>
									<span class="text-[11px] text-gray-500 dark:text-gray-400">{{ $s['used'] }}GB / {{ $s['limit'] }}GB ({{ $percent }}%)</span>
								</div>
								<div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
									<div class="h-full {{ $bar }} rounded-lg" style="width: {{ $percent }}%"></div>
								</div>
							</li>
						@endforeach
					</ul>
					<div class="mt-4 text-right">
						<button @click="open('showStorage')" class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 inline-flex items-center gap-1"><i data-feather='maximize-2' class='w-3 h-3'></i> Detail</button>
					</div>
				</div>

				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5">
					<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 mb-4 text-sm"><i data-feather="alert-circle" class="w-4 h-4 text-rose-500"></i> Log & Aktivitas</h2>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						@foreach($errors as $e)
							<li class="py-3 flex items-start gap-3">
								<span class="text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5">{{ $e['time'] }}</span>
								<div class="flex-1">
									@php
										$levelColor = match($e['level']) {
											'ERROR' => 'text-rose-500 dark:text-rose-400',
											'WARNING' => 'text-amber-600 dark:text-amber-400',
											default => 'text-emerald-600 dark:text-emerald-400'
										};
									@endphp
									<div class="text-[11px] font-semibold {{ $levelColor }}">{{ $e['level'] }}</div>
									<div class="text-gray-600 dark:text-gray-300 leading-snug">{{ $e['message'] }}</div>
								</div>
							</li>
						@endforeach
					</ul>
					<div class="mt-4 text-right">
						<button @click="open('showLogs')" class="text-xs px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-600 dark:text-gray-300 inline-flex items-center gap-1"><i data-feather='maximize-2' class='w-3 h-3'></i> Detail</button>
					</div>
				</div>
			</div>
		</div>

		@include('pages.admin.monitoring.detail.health-modal')
		@include('pages.admin.monitoring.detail.queue-modal')
		@include('pages.admin.monitoring.detail.logs-modal')
		@include('pages.admin.monitoring.detail.storage-modal')
		@include('pages.admin.monitoring.detail.numbering-modal')

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie</div>
	</div>
</x-app-layout>
