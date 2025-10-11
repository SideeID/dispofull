<template x-if="showAttachment">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Lampiran Surat</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-4 text-sm">
				<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-60 overflow-y-auto pr-1">
					<template x-if="!selected?.attachments_list || !selected.attachments_list.length">
						<li class="text-gray-400">Tidak ada lampiran</li>
					</template>
					<template x-for="a in (selected?.attachments_list || [])" :key="a.url + a.name">
						<li class="flex items-center justify-between gap-3 p-2 rounded bg-gray-50 dark:bg-gray-700/40">
							<div class="flex items-center gap-2">
								<i data-feather="file" class="w-4 h-4 text-indigo-500"></i>
								<span x-text="a.name"></span>
							</div>
							<div class="flex items-center gap-3">
								<span class="text-[10px] text-gray-400" x-text="a.size_human || ''"></span>
								<a :href="a.url" target="_blank" class="text-indigo-600 dark:text-indigo-400 hover:underline text-[11px]">Unduh</a>
							</div>
						</li>
					</template>
				</ul>
				<div class="pt-2 border-t border-dashed border-gray-200 dark:border-gray-700/60">
					<form class="space-y-3" @submit.prevent="alert('Upload lampiran (dummy)')">
						<div>
							<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tambah Lampiran</label>
							<input type="file" class="block w-full text-[11px] text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-500/10 dark:file:text-indigo-300" />
							<p class="mt-1 text-[10px] text-gray-400">PDF / Gambar (max 5MB)</p>
						</div>
						<div class="flex items-center justify-end gap-3">
							<button type="button" @click="closeAll()" class="px-3 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Tutup</button>
							<button type="submit" class="px-3 py-2 rounded-lg text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium">Upload</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>
