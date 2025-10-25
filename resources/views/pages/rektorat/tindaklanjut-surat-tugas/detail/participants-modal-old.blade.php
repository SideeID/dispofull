<template x-if="showParticipants">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Peserta Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || selected?.temp"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="grid md:grid-cols-2 gap-6 text-sm">
				<div class="space-y-4">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 h-full">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-3">Daftar Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-56 overflow-y-auto pr-1">
							<li class="flex items-center justify-between gap-2"><span>Dr. Andi · Ketua</span><button class="text-rose-500 hover:underline">Hapus</button></li>
							<li class="flex items-center justify-between gap-2"><span>Ir. Budi · Anggota</span><button class="text-rose-500 hover:underline">Hapus</button></li>
							<li class="flex items-center justify-between gap-2"><span>Dr. Citra · Anggota</span><button class="text-rose-500 hover:underline">Hapus</button></li>
							<li class="flex items-center justify-between gap-2"><span>Sari, M.Sc · Anggota</span><button class="text-rose-500 hover:underline">Hapus</button></li>
						</ul>
					</div>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Tambah Peserta</div>
						<form class="space-y-4" @submit.prevent="alert('Tambah peserta (dummy)')">
							<div>
								<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Nama</label>
								<input type="text" class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500" placeholder="Nama Peserta" />
							</div>
							<div class="grid grid-cols-2 gap-4">
								<div>
									<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Peran</label>
									<select class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500">
										<option value="anggota">Anggota</option>
										<option value="ketua">Ketua</option>
									</select>
								</div>
								<div>
									<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Unit</label>
									<input type="text" class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500" placeholder="Unit" />
								</div>
							</div>
							<div class="flex items-center justify-end">
								<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Tambah</button>
							</div>
						</form>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Log Perubahan Peserta</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-28 overflow-y-auto pr-1">
							<li>2025-10-02 09:40 · Menambah Sari, M.Sc</li>
							<li>2025-10-02 09:35 · Mengubah peran Budi menjadi Anggota</li>
						</ul>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
