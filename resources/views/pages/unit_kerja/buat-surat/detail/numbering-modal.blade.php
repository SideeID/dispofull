<template x-if="openFlags.showNumbering">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Konfigurasi Penomoran</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Atur format nomor surat.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 text-sm">
				<div class="grid gap-6">
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Prefix</label>
						<input type="text" x-model="form.nomor.prefix" @input="markChanged()" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Sequence (Urutan)</label>
						<div class="flex items-center gap-2">
							<button type="button" @click="decrementSeq()" class="px-2 py-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600"><i data-feather='minus' class='w-3.5 h-3.5'></i></button>
							<input type="number" x-model="form.nomor.seq" @input="markChanged()" class="w-24 text-center rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
							<button type="button" @click="incrementSeq()" class="px-2 py-1.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600"><i data-feather='plus' class='w-3.5 h-3.5'></i></button>
						</div>
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Kode Unit</label>
						<select x-model="form.nomor.unit" @change="markChanged()" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500 text-gray-700 dark:text-gray-100">
							<option value="">Pilih Unit/Departemen</option>
							<template x-for="dept in availableDepartments" :key="dept.id">
								<option :value="dept.code" x-text="`${dept.code} - ${dept.name}`"></option>
							</template>
						</select>
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Tahun</label>
						<input type="number" x-model="form.nomor.tahun" @input="markChanged()" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
					</div>
					<div class="flex flex-col gap-1.5">
						<label class="text-xs font-medium text-gray-600 dark:text-gray-300">Preview</label>
						<div class="px-4 py-2 rounded-lg bg-gray-50 dark:bg-gray-700 border border-dashed border-gray-300 dark:border-gray-600 font-mono text-xs text-violet-600 dark:text-violet-400" x-text="computedNumber()"></div>
					</div>
				</div>
			</div>
			<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-3 bg-white dark:bg-gray-800 rounded-b-2xl">
				<div class="text-[10px] text-gray-400">Nomor akan otomatis terkunci saat pengajuan penandatanganan.</div>
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
			</div>
		</div>
	</div>
</template>
