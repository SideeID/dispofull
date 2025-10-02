<template x-if="showView">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Departemen</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Informasi lengkap departemen terpilih.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="text-sm space-y-4 max-h-[55vh] overflow-y-auto pr-2">
				<div class="grid grid-cols-3 gap-3">
					<div class="text-gray-500 dark:text-gray-400 text-xs">Kode</div>
					<div class="col-span-2 font-medium text-gray-800 dark:text-gray-100" x-text="selected?.code"></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Nama</div>
					<div class="col-span-2 font-medium text-gray-800 dark:text-gray-100" x-text="selected?.name"></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Tipe</div>
					<div class="col-span-2"><span x-text="selected?.type" class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300"></span></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Status</div>
					<div class="col-span-2"><span x-text="selected?.active ? 'Aktif':'Nonaktif'" class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300"></span></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Surat Masuk</div>
					<div class="col-span-2 font-medium" x-text="selected?.in"></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Surat Keluar</div>
					<div class="col-span-2 font-medium" x-text="selected?.out"></div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
					Tutup
				</button>
			</div>
		</div>
	</div>
</template>
