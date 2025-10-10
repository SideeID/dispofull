<template x-if="showExport">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Export Data</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ekspor daftar surat masuk.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-5 text-sm" @submit.prevent="exportData()">
				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Format</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
							<option value="xlsx">Excel (.xlsx)</option>
							<option value="csv">CSV (.csv)</option>
							<option value="pdf">PDF (.pdf)</option>
						</select>
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Rentang</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:focus:ring-indigo-500 text-gray-700 dark:text-gray-100">
							<option value="current">Halaman Ini</option>
							<option value="filtered">Hasil Filter</option>
							<option value="all">Semua Data</option>
						</select>
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Kolom</label>
					<div class="grid grid-cols-2 gap-2 text-[11px] text-gray-600 dark:text-gray-300">
						<label class="flex items-center gap-2"><input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Nomor</label>
						<label class="flex items-center gap-2"><input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Perihal</label>
						<label class="flex items-center gap-2"><input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Pengirim</label>
						<label class="flex items-center gap-2"><input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Kategori</label>
						<label class="flex items-center gap-2"><input type="checkbox" checked class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Tgl Surat</label>
						<label class="flex items-center gap-2"><input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Prioritas</label>
						<label class="flex items-center gap-2"><input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Status</label>
						<label class="flex items-center gap-2"><input type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 focus:ring-indigo-500" /> Disposisi</label>
					</div>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2"><i data-feather='download' class='w-4 h-4'></i>Export</button>
				</div>
			</form>
			<div class="text-[10px] text-gray-400">Export besar akan diproses background (future queue).</div>
		</div>
	</div>
</template>
