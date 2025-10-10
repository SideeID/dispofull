<template x-if="showHistory">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Riwayat Detail</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="max-h-[55vh] overflow-y-auto pr-1 text-sm">
				<ul class="space-y-4" x-data="{ logs: [] }" x-init="logs = [
					{time:'2025-10-02 11:30',actor:'SPI',action:'Menandai selesai',status:'success'},
					{time:'2025-10-02 10:45',actor:'WR II',action:'Memberi instruksi ke SPI',status:'success'},
					{time:'2025-10-02 09:50',actor:'Rektor',action:'Meneruskan ke WR II',status:'success'},
					{time:'2025-10-02 09:25',actor:'Sekretariat',action:'Mendisposisikan ke Rektor',status:'success'},
					{time:'2025-10-02 09:10',actor:'Sekretariat',action:'Menerima surat',status:'success'},
				]">
					<template x-for="l in logs" :key="l.time + l.actor">
						<li class="flex items-start gap-3">
							<div class="w-10 text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5" x-text="l.time.split(' ')[1]"></div>
							<div class="flex-1">
								<div class="text-[11px] font-semibold" :class="{
									'text-emerald-600 dark:text-emerald-400': l.status=='success',
									'text-amber-600 dark:text-amber-400': l.status=='warning',
									'text-rose-600 dark:text-rose-400': l.status=='error',
									'text-slate-500 dark:text-slate-400': l.status=='info'
								}" x-text="l.actor"></div>
								<div class="text-gray-600 dark:text-gray-300 leading-snug" x-text="l.action"></div>
								<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="l.time"></div>
							</div>
						</li>
					</template>
				</ul>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-violet-600 hover:bg-violet-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
