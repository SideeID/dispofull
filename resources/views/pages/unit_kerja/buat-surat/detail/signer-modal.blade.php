<template x-if="showSigner">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Pilih Penandatangan</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Penandatangan bertanggung jawab atas validitas surat.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="flex-1 overflow-y-auto px-6 py-6 space-y-4 text-sm" x-data="{ search:'' }">
				<div class="relative">
					<i data-feather='search' class='w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
					<input x-model="search" type="text" placeholder="Cari nama / jabatan" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
				</div>
				<ul class="divide-y divide-gray-100 dark:divide-gray-700/60" x-data="{ filtered(){ return signers.filter(s=> !search || s.nama.toLowerCase().includes(search.toLowerCase()) || s.jabatan.toLowerCase().includes(search.toLowerCase())); } }">
					<template x-for="s in filtered()" :key="s.id">
						<li class="py-3 flex items-center gap-4">
							<div class="flex-1 min-w-0">
								<div class="flex items-center gap-2">
									<span class="text-xs font-semibold text-gray-800 dark:text-gray-100" x-text="s.nama"></span>
									<template x-if="signer && signer.id===s.id"><span class="text-[10px] px-1.5 py-0.5 rounded bg-violet-500/10 text-violet-600 dark:text-violet-400">Terpilih</span></template>
								</div>
								<div class="text-[11px] text-gray-500 dark:text-gray-400" x-text="s.jabatan"></div>
								<div class="text-[11px] text-gray-400 dark:text-gray-500 font-mono" x-text="s.nip"></div>
							</div>
							<div class="flex items-center gap-2">
								<button type="button" @click="setSigner(s)" class="px-3 py-1.5 rounded bg-violet-600 hover:bg-violet-500 text-white text-[11px]">Pilih</button>
							</div>
						</li>
					</template>
					<li class="py-6 text-center text-[11px] text-gray-400" x-show="filtered().length===0">Tidak ada penandatangan.</li>
				</ul>
			</div>
			<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-3 bg-white dark:bg-gray-800 rounded-b-2xl">
				<div class="text-[10px] text-gray-400">Penandatangan dapat diubah sampai surat diajukan.</div>
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
			</div>
		</div>
	</div>
</template>
