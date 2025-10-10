<template x-if="showAttachment">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Lampiran Surat (Arsip)</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-4 text-sm">
				<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
					<li class="flex items-center justify-between gap-3 p-2 rounded bg-gray-50 dark:bg-gray-700/40"><span>laporan_kegiatan.pdf</span><button class="text-slate-500 dark:text-slate-400 hover:underline">Unduh</button></li>
					<li class="flex items-center justify-between gap-3 p-2 rounded bg-gray-50 dark:bg-gray-700/40"><span>foto_kegiatan.zip</span><button class="text-slate-500 dark:text-slate-400 hover:underline">Unduh</button></li>
				</ul>
				<p class="text-[11px] text-gray-400 dark:text-gray-500">Lampiran hanya-baca. Tidak dapat diubah pada arsip.</p>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
