<template x-if="showNumbering">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Pemakaian Penomoran</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rincian pola & counter nomor surat.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="overflow-x-auto -mx-2">
				<table class="min-w-full text-sm">
					<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
					<tr>
						<th class="text-left px-4 py-2 font-semibold">Kode</th>
						<th class="text-left px-4 py-2 font-semibold">Nama</th>
						<th class="text-left px-4 py-2 font-semibold">Current</th>
						<th class="text-left px-4 py-2 font-semibold">Reset</th>
						<th class="text-left px-4 py-2 font-semibold">Pattern</th>
						<th class="text-left px-4 py-2 font-semibold">Last Issued</th>
					</tr>
					</thead>
					<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60" x-data="{ rows: [] }" x-init="rows = [
						{code:'UND',name:'Undangan',current:182,reset:'YEARLY',pattern:'{SEQ}/UND/BKR/{ROMAN_MONTH}/{YEAR}',last_issued:'2025-10-02 09:12'},
						{code:'SK',name:'Surat Keputusan',current:44,reset:'YEARLY',pattern:'{SEQ}/SK/UB/{YEAR}',last_issued:'2025-10-02 08:40'},
						{code:'INT',name:'Internal Memo',current:12,reset:'MONTHLY',pattern:'{SEQ}/INT/{MONTH}/{YEAR}',last_issued:'2025-10-01 16:05'},
					]">
						<template x-for="n in rows" :key="n.code">
							<tr>
								<td class="px-4 py-2 font-mono text-xs text-gray-700 dark:text-gray-200" x-text="n.code"></td>
								<td class="px-4 py-2 font-medium text-gray-800 dark:text-gray-100" x-text="n.name"></td>
								<td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="n.current"></td>
								<td class="px-4 py-2 text-gray-600 dark:text-gray-300" x-text="n.reset"></td>
								<td class="px-4 py-2 text-[11px] font-mono text-gray-500 dark:text-gray-400" x-text="n.pattern"></td>
								<td class="px-4 py-2 text-[11px] text-gray-500 dark:text-gray-400" x-text="n.last_issued"></td>
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
