<template x-if="showConfirm">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Konfirmasi Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="text-sm space-y-4">
				<p class="text-gray-600 dark:text-gray-300">Tindakan ini akan mengirim surat ke tahap tanda tangan. Pastikan data sudah benar.</p>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan (opsional)</label>
					<textarea x-model="confirmNote" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" rows="3" placeholder="Tambahkan catatan untuk sekretariat/penandatangan..."></textarea>
				</div>
			</div>
			<div class="flex items-center justify-end gap-2 pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
				<button type="button" @click="submitConfirm()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium">Konfirmasi</button>
			</div>
		</div>
	</div>
</template>
