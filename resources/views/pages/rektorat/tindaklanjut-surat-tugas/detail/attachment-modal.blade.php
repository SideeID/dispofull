<template x-if="showAttachments">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Lampiran Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || selected?.temp"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-4 text-sm">
				<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
					<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Daftar Lampiran</div>
					<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-60 overflow-y-auto pr-1">
						<li class="flex items-center justify-between gap-3 p-2 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
							<div class="flex items-center gap-2"><i data-feather="file" class="w-3.5 h-3.5 text-emerald-500"></i><span>tor_kegiatan.pdf</span></div>
							<div class="flex items-center gap-1">
								<button class="text-emerald-600 dark:text-emerald-400 hover:underline text-[11px]">Lihat</button>
								<button class="text-rose-500 hover:underline text-[11px]">Hapus</button>
							</div>
						</li>
					</ul>
				</div>
				<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
					<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Tambah Lampiran</div>
					<form class="space-y-3" @submit.prevent="alert('Upload lampiran (dummy)')">
						<input type="file" class="block w-full text-[11px] text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-500/10 dark:file:text-emerald-300" />
						<p class="text-[10px] text-gray-400">PDF / DOC / Gambar (max 5MB)</p>
						<div class="flex items-center justify-end">
							<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Upload</button>
						</div>
					</form>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
