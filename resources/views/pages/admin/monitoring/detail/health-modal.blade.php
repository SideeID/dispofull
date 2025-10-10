<template x-if="showHealth">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Kesehatan Komponen</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ringkasan status & latency komponen inti.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="overflow-x-auto -mx-2">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
					<tr>
						<th class="text-left px-4 py-2 font-semibold">Komponen</th>
						<th class="text-left px-4 py-2 font-semibold">Status</th>
						<th class="text-left px-4 py-2 font-semibold">Latency</th>
						<th class="text-left px-4 py-2 font-semibold">Catatan</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60" x-data="{ items: [] }" x-init="items = [
						{key:'database',name:'Database',status:'ok',latency_ms:12,detail:'Koneksi stabil & respons cepat'},
						{key:'cache',name:'Cache',status:'ok',latency_ms:3,detail:'Redis terhubung, hit ratio 92%'},
						{key:'queue',name:'Queue Worker',status:'warn',latency_ms:0,detail:'1 job tertunda > 5m'},
						{key:'mail',name:'Mail Transport',status:'ok',latency_ms:48,detail:'SMTP respons normal'},
						{key:'storage',name:'Storage Disk',status:'ok',latency_ms:0,detail:'Pemakaian 63% dari kuota'},
						{key:'sign',name:'Digital Signature',status:'ok',latency_ms:110,detail:'Service tanda tangan respons normal'},
					]">
						<template x-for="h in items" :key="h.key">
							<tr>
								<td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100" x-text="h.name"></td>
								<td class="px-4 py-2">
									<template x-if="h.status=='ok'"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">OK</span></template>
									<template x-if="h.status=='warn'"><span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">WARN</span></template>
								</td>
								<td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="h.latency_ms + ' ms'"></td>
								<td class="px-4 py-2 text-gray-500 dark:text-gray-400 text-xs" x-text="h.detail"></td>
							</tr>
						</template>
					</tbody>
				</table>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
