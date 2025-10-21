<template x-if="showRoute">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Alur Disposisi</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="grid md:grid-cols-5 gap-5 text-sm">
				<div class="md:col-span-3 space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-3">Timeline</div>
						<ol class="space-y-4 text-xs text-gray-600 dark:text-gray-300">
							<template x-for="(s,i) in steps" :key="s.time + s.unit">
								<li class="flex items-start gap-3">
									<div class="flex flex-col items-center">
										<div class="w-3 h-3 rounded-full" :class="{'bg-emerald-500': s.status=='success', 'bg-amber-500': s.status=='pending'}"></div>
										<div class="w-px flex-1 bg-gradient-to-b from-emerald-400/40 to-transparent" x-show="i < steps.length-1"></div>
									</div>
									<div class="flex-1">
										<div class="flex items-center gap-2">
											<span class="font-semibold text-gray-700 dark:text-gray-200" x-text="s.unit"></span>
											<span class="text-[10px] font-mono text-gray-400 dark:text-gray-500" x-text="s.time.split(' ')[1]"></span>
										</div>
										<div class="text-gray-600 dark:text-gray-300 leading-snug" x-text="s.action"></div>
										<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="s.time"></div>
									</div>
								</li>
							</template>
						</ol>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-3">Ringkasan</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>Total langkah: <span class="font-semibold" x-text="selected?.chain"></span></li>
							<li>Durasi total (dummy): 2h 20m</li>
							<li>Status akhir: <span x-text="selected?.status"></span></li>
						</ul>
					</div>
				</div>
				<div class="md:col-span-2 space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Distribusi Unit</div>
						<div class="grid grid-cols-3 gap-3 text-[11px] text-gray-600 dark:text-gray-300">
							<div class="bg-white dark:bg-gray-800 rounded-lg p-2 text-center"><div class="font-semibold text-violet-600 dark:text-violet-400">2</div><div>Sekretariat</div></div>
							<div class="bg-white dark:bg-gray-800 rounded-lg p-2 text-center"><div class="font-semibold text-violet-600 dark:text-violet-400">1</div><div>Rektor</div></div>
							<div class="bg-white dark:bg-gray-800 rounded-lg p-2 text-center"><div class="font-semibold text-violet-600 dark:text-violet-400">1</div><div>WR II</div></div>
							<div class="bg-white dark:bg-gray-800 rounded-lg p-2 text-center"><div class="font-semibold text-violet-600 dark:text-violet-400">1</div><div>SPI</div></div>
						</div>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Aktivitas Terkait</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-40 overflow-y-auto pr-1">
							<li>2025-10-02 11:35 · Notifikasi selesai dikirim</li>
							<li>2025-10-02 11:32 · Log pembubuhan catatan</li>
							<li>2025-10-02 10:50 · Instruksi tambahan</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-violet-600 hover:bg-violet-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
