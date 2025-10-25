<template x-if="showExport">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Export Arsip</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh data arsip surat tugas dalam format CSV</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form method="GET" action="{{ route('unit_kerja.archives.export') }}" class="space-y-6 text-sm">
				<!-- Copy filter parameters from current page -->
				<input type="hidden" name="q" value="{{ request('q') }}" />
				<input type="hidden" name="date" value="{{ request('date') }}" />
				<input type="hidden" name="priority" value="{{ request('priority') }}" />
				<input type="hidden" name="start_from" value="{{ request('start_from') }}" />
				<input type="hidden" name="end_to" value="{{ request('end_to') }}" />
				
				<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
					<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Info Export</div>
					<ul class="space-y-1 text-xs text-gray-600 dark:text-gray-300">
						<li class="flex items-center gap-2">
							<i data-feather='check-circle' class='w-3.5 h-3.5 text-emerald-500'></i>
							<span>Format: CSV (Excel Compatible)</span>
						</li>
						<li class="flex items-center gap-2">
							<i data-feather='check-circle' class='w-3.5 h-3.5 text-emerald-500'></i>
							<span>Maksimal: 5000 records</span>
						</li>
						<li class="flex items-center gap-2">
							<i data-feather='check-circle' class='w-3.5 h-3.5 text-emerald-500'></i>
							<span>Menggunakan filter yang sedang aktif</span>
						</li>
					</ul>
				</div>
				
				<div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-3">
					<div class="flex items-start gap-2 text-xs text-amber-800 dark:text-amber-300">
						<i data-feather='alert-triangle' class='w-4 h-4 mt-0.5 shrink-0'></i>
						<div>
							<div class="font-semibold mb-1">Filter yang digunakan:</div>
							<ul class="space-y-0.5 list-disc list-inside">
								@if(request('q'))
								<li>Pencarian: "{{ request('q') }}"</li>
								@endif
								@if(request('date'))
								<li>Tanggal: {{ request('date') }}</li>
								@endif
								@if(request('priority'))
								<li>Prioritas: {{ request('priority') }}</li>
								@endif
								@if(request('start_from') || request('end_to'))
								<li>Periode: {{ request('start_from') ?: '...' }} s/d {{ request('end_to') ?: '...' }}</li>
								@endif
								@if(!request()->hasAny(['q', 'date', 'priority', 'start_from', 'end_to']))
								<li>Semua arsip akan di-export</li>
								@endif
							</ul>
						</div>
					</div>
				</div>
				
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
						<i data-feather='download' class='w-3.5 h-3.5'></i>
						<span>Download CSV</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
