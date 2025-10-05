<template x-if="showSubmitConfirm">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Ajukan Tanda Tangan?</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Periksa kembali data surat sebelum mengajukan.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-4 text-sm">
				<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 space-y-2">
					<div class="flex items-center justify-between text-xs">
						<span class="font-semibold text-gray-600 dark:text-gray-300">Nomor Surat</span>
						<span class="font-mono text-violet-600 dark:text-violet-400" x-text="computedNumber()"></span>
					</div>
					<div class="flex items-center justify-between text-xs">
						<span class="font-semibold text-gray-600 dark:text-gray-300">Jenis</span>
						<span x-text="form.jenis || '—'"></span>
					</div>
					<div class="flex items-center justify-between text-xs">
						<span class="font-semibold text-gray-600 dark:text-gray-300">Perihal</span>
						<span class="truncate max-w-[12rem]" x-text="form.perihal || '—'"></span>
					</div>
					<div class="flex items-center justify-between text-xs">
						<span class="font-semibold text-gray-600 dark:text-gray-300">Penandatangan</span>
						<span x-text="signer ? signer.nama : 'Belum dipilih'"></span>
					</div>
					<div class="flex items-center justify-between text-xs">
						<span class="font-semibold text-gray-600 dark:text-gray-300">Lampiran</span>
						<span x-text="attachments.length + ' file'"></span>
					</div>
				</div>
				<div class="text-[11px] text-amber-600 dark:text-amber-400 flex items-start gap-2">
					<i data-feather='alert-triangle' class='w-4 h-4 mt-0.5 shrink-0'></i>
					<div>Setelah diajukan, nomor dan penandatangan tidak dapat diubah. Anda masih bisa menambahkan lampiran sebelum penandatangan menandatangani.</div>
				</div>
			</div>
			<div class="flex items-center justify-end gap-3 pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Batal</button>
				<button type="button" @click="submit()" class="px-4 py-2 rounded-lg text-sm bg-violet-600 hover:bg-violet-500 text-white flex items-center gap-2"><i data-feather='send' class='w-4 h-4'></i>Ajukan</button>
			</div>
		</div>
	</div>
</template>
