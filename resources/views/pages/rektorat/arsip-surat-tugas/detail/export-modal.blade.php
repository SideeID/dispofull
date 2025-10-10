<template x-if="showExport">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Export Arsip</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unduh data arsip surat tugas</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-6 text-sm" @submit.prevent="alert('Export (dummy)'); closeAll()">
				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Format</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500">
							<option value="xlsx">Excel (XLSX)</option>
							<option value="csv">CSV</option>
							<option value="pdf">PDF</option>
						</select>
					</div>
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Rentang Waktu</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500">
							<option value="30">30 Hari</option>
							<option value="90">90 Hari</option>
							<option value="365">1 Tahun</option>
							<option value="all">Semua</option>
						</select>
					</div>
				</div>
				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500">
							<option value="">Semua</option>
							<option value="low">Low</option>
							<option value="normal">Normal</option>
							<option value="high">High</option>
							<option value="urgent">Urgent</option>
						</select>
					</div>
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Kolom Tambahan</label>
						<select multiple size="3" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-[11px] focus:outline-none focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-500">
							<option value="participants">Jumlah Peserta</option>
							<option value="files">Jumlah Lampiran</option>
							<option value="duration">Durasi Hari</option>
							<option value="archived_at">Tanggal Arsip</option>
						</select>
					</div>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-slate-700 hover:bg-slate-600 text-white font-medium">Export</button>
				</div>
			</form>
		</div>
	</div>
</template>
