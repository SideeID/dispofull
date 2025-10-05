<template x-if="showSign">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tanda Tangan Digital</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-4 text-sm">
				<div class="bg-gray-50 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 text-center">
					<p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Area tanda tangan (placeholder). Integrasikan komponen tanda tangan (canvas) atau unggahan.</p>
					<div class="h-32 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 flex items-center justify-center text-[11px] text-gray-400">Signature Pad</div>
				</div>
				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Metode</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
							<option value="digital">Digital</option>
							<option value="electronic">Electronic</option>
						</select>
					</div>
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Jenis File</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
							<option value="pdf">PDF</option>
							<option value="image">Image</option>
						</select>
					</div>
				</div>
				<div>
					<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan (opsional)</label>
					<textarea rows="3" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Catatan untuk log..."></textarea>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="button" @click="alert('Tanda tangan (dummy)'); closeAll()" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Tandatangani</button>
				</div>
			</div>
		</div>
	</div>
</template>
