<template x-if="openFlags.showHistory">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[85vh]">
			<div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Riwayat Draft</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perubahan dan aksi terhadap draft surat.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="flex-1 overflow-y-auto px-6 py-6 text-sm">
				<ul class="space-y-4" x-data="{ logs: history }">
					<template x-for="l in logs" :key="l.time + l.note">
						<li class="flex items-start gap-3">
							<div class="w-14 text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5" x-text="l.time.split(' ')[1]"></div>
							<div class="flex-1">
								<div class="text-gray-700 dark:text-gray-200 leading-snug" x-text="l.note"></div>
								<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="l.time"></div>
							</div>
						</li>
					</template>
					<li class="text-[11px] text-gray-400" x-show="logs.length===0">Belum ada riwayat.</li>
				</ul>
			</div>
			<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-end gap-3 bg-white dark:bg-gray-800 rounded-b-2xl">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
			</div>
		</div>
	</div>
</template>
