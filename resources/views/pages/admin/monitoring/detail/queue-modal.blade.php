<template x-if="showQueue">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Antrian & Jobs</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Status antrian, job pending & kegagalan.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="overflow-x-auto -mx-2">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
					<tr>
						<th class="text-left px-4 py-2 font-semibold">Queue</th>
						<th class="text-left px-4 py-2 font-semibold">Pending</th>
						<th class="text-left px-4 py-2 font-semibold">Processing</th>
						<th class="text-left px-4 py-2 font-semibold">Failed</th>
						<th class="text-left px-4 py-2 font-semibold">Oldest Wait (s)</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60" x-data="{ rows: [] }" x-init="rows = [
						{name:'default',pending:3,processing:1,failed:0,oldest_wait_s:340},
						{name:'emails',pending:0,processing:0,failed:1,oldest_wait_s:0},
						{name:'signing',pending:2,processing:1,failed:0,oldest_wait_s:95},
					]">
						<template x-for="q in rows" :key="q.name">
							<tr>
								<td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100" x-text="q.name"></td>
								<td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="q.pending"></td>
								<td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="q.processing"></td>
								<td class="px-4 py-2" x-html="q.failed>0 ? `<span class='px-2 py-0.5 rounded-full text-[11px] font-medium bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-300'>${q.failed} gagal</span>` : '<span class=\'text-gray-400 text-xs\'>0</span>'"></td>
								<td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="q.oldest_wait_s"></td>
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
