<template x-if="showEdit">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Edit Jenis Surat</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Perbarui data jenis surat.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-4" @submit.prevent="alert('Submit edit jenis surat (dummy)'); closeAll()">
				<div class="grid md:grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kode</label>
						<input type="text" x-model="selected.code" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Nama</label>
						<input type="text" x-model="selected.name" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kategori</label>
						<select x-model="selected.category" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="undangan">Undangan</option>
							<option value="keputusan">Keputusan</option>
							<option value="internal">Internal</option>
							<option value="eksternal">Eksternal</option>
						</select>
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
						<select x-model="selected.active" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="1">Aktif</option>
							<option value="0">Nonaktif</option>
						</select>
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Format Nomor</label>
					<input type="text" x-model="selected.format" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					<p class="mt-1 text-[11px] text-gray-400 dark:text-gray-500">Token: {SEQ}, {YEAR}, {MONTH}, {ROMAN_MONTH}, {DAY}, {CODE}</p>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Deskripsi (opsional)</label>
					<textarea rows="3" x-text="selected.description" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Catatan tambahan"></textarea>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
						Simpan Perubahan
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
