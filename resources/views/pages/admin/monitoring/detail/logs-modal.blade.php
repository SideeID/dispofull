<template x-if="showLogs">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Log & Aktivitas Sistem</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cuplikan peristiwa terbaru & status.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="max-h-[55vh] overflow-y-auto pr-2">
				<ul class="space-y-4 text-sm" x-data="{ items: [] }" x-init="items = [
					{time:'09:21:04',level:'WARNING',message:'Queue delay > 300s (default)'},
					{time:'08:55:18',level:'ERROR',message:'Gagal kirim email notifikasi disposisi ID #442'},
					{time:'08:10:07',level:'INFO',message:'Regenerasi nomor surat (cron) selesai'},
					{time:'07:45:33',level:'INFO',message:'Worker queue: 3 job diproses'},
					{time:'07:02:11',level:'WARNING',message:'Utilisasi storage lampiran > 80%'},
				]">
					<template x-for="l in items" :key="l.time + l.message">
						<li class="flex items-start gap-3">
							<span class="text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5" x-text="l.time"></span>
							<div class="flex-1">
								<div class="text-[11px] font-semibold" :class="{
									'text-rose-500 dark:text-rose-400': l.level=='ERROR',
									'text-amber-600 dark:text-amber-400': l.level=='WARNING',
									'text-emerald-600 dark:text-emerald-400': l.level=='INFO'
								}" x-text="l.level"></div>
								<div class="text-gray-600 dark:text-gray-300 leading-snug" x-text="l.message"></div>
							</div>
						</li>
					</template>
				</ul>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
