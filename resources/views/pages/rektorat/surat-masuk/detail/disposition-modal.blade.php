<template x-if="showDisposition">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Disposisi Surat</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="grid md:grid-cols-2 gap-6 text-sm">
				<div class="space-y-4">
					<form class="space-y-4" @submit.prevent="alert('Submit disposisi (dummy)'); closeAll()">
						<div>
							<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kepada</label>
							<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
								<option value="wr1">Wakil Rektor I</option>
								<option value="wr2">Wakil Rektor II</option>
								<option value="p3m">P3M</option>
								<option value="baa">BAA</option>
							</select>
						</div>
						<div>
							<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Instruksi</label>
							<textarea rows="3" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Tindak lanjuti ..."></textarea>
						</div>
						<div class="grid grid-cols-2 gap-4">
							<div>
								<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
								<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
									<option value="normal">Normal</option>
									<option value="high">High</option>
									<option value="urgent">Urgent</option>
								</select>
							</div>
							<div>
								<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Batas Waktu</label>
								<input type="date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
							</div>
						</div>
						<div class="flex items-center justify-end gap-3 pt-2">
							<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
							<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Simpan Disposisi</button>
						</div>
					</form>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Riwayat Disposisi</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li><span class="font-semibold">2025-10-02 09:21</span> · Ke WR I · Kajian akademik & rekomendasi</li>
							<li><span class="font-semibold">2025-10-02 08:12</span> · Ke P3M · Verifikasi data</li>
						</ul>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Status Pengerjaan</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
							<li>WR I · <span class="text-amber-600 dark:text-amber-400">Menunggu Tindak Lanjut</span></li>
							<li>P3M · <span class="text-blue-600 dark:text-blue-400">Sedang Diproses</span></li>
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
