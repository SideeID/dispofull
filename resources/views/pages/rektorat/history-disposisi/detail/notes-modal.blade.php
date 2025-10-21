<template x-if="showNotes">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Catatan Disposisi</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-5 text-sm">
				<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
					<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Daftar Catatan</div>
					<ul class="space-y-3 text-xs text-gray-600 dark:text-gray-300 max-h-60 overflow-y-auto pr-1">
						<template x-for="n in notes" :key="n.time + n.unit">
							<li class="flex items-start gap-3">
								<div class="w-12 text-[11px] font-mono text-gray-400 dark:text-gray-500" x-text="n.time.split(' ')[1]"></div>
								<div class="flex-1">
									<div class="text-[11px] font-semibold text-violet-600 dark:text-violet-400" x-text="n.unit"></div>
									<div class="text-gray-600 dark:text-gray-300 leading-snug" x-text="n.text"></div>
									<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="n.time"></div>
								</div>
							</li>
						</template>
					</ul>
				</div>
				<!-- Form tambah catatan dinonaktifkan karena belum ada endpoint -->
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-violet-600 hover:bg-violet-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
