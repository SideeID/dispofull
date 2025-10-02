<template x-if="showStorage">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Penggunaan Storage</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rincian pemakaian per kategori.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-5 text-sm" x-data="{ list: [] }" x-init="list = [
				{name:'Surat Masuk', used:3.2, limit:10},
				{name:'Surat Keluar', used:1.8, limit:10},
				{name:'Lampiran', used:6.5, limit:15},
				{name:'Tanda Tangan', used:0.4, limit:2},
			]">
				<template x-for="s in list" :key="s.name">
					<div>
						<div class="flex items-center justify-between mb-1">
							<span class="font-medium text-gray-700 dark:text-gray-200" x-text="s.name"></span>
							<span class="text-[11px] text-gray-500 dark:text-gray-400" x-text="`${s.used}GB / ${s.limit}GB (${Math.round((s.used/s.limit)*100)}%)`"></span>
						</div>
						<div class="h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
							<div class="h-full rounded-full" :class="Math.round((s.used/s.limit)*100)>80 ? 'bg-rose-500' : 'bg-amber-500'" :style="`width: ${Math.round((s.used/s.limit)*100)}%`"></div>
						</div>
					</div>
				</template>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
