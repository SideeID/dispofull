<template x-if="showParticipants">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Peserta Surat (Arsip)</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="grid md:grid-cols-2 gap-6 text-sm">
				<div class="space-y-4">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 h-full">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-3">Daftar Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-56 overflow-y-auto pr-1">
							<li>Dr. Andi · Ketua</li>
							<li>Ir. Budi · Anggota</li>
							<li>Dr. Citra · Anggota</li>
							<li>Sari, M.Sc · Anggota</li>
						</ul>
					</div>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Statistik Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>Total: 5 Orang</li>
							<li>Ketua: 1</li>
							<li>Anggota: 4</li>
						</ul>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Log Peserta (Terakhir)</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-28 overflow-y-auto pr-1">
							<li>2025-09-29 09:40 · Menambah Sari, M.Sc</li>
							<li>2025-09-29 09:35 · Mengubah peran Budi menjadi Anggota</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
