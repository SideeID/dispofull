<template x-if="showView && selectedUser">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showView=false"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Pengguna</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selectedUser.name"></p>
				</div>
				<button @click="showView=false" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="text-sm space-y-4 max-h-[55vh] overflow-y-auto pr-2">
				<div class="grid grid-cols-3 gap-3">
					<div class="text-gray-500 dark:text-gray-400 text-xs">Nama</div>
					<div class="col-span-2 font-medium text-gray-800 dark:text-gray-100" x-text="selectedUser.name"></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Email</div>
					<div class="col-span-2 text-gray-700 dark:text-gray-300" x-text="selectedUser.email"></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">NIP</div>
					<div class="col-span-2 text-gray-700 dark:text-gray-300" x-text="selectedUser.nip || '-' "></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Departemen</div>
					<div class="col-span-2 text-gray-700 dark:text-gray-300" x-text="selectedUser.department"></div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Role</div>
					<div class="col-span-2">
						<span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold" :class="{
							'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-300': selectedUser.role=='admin',
							'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-300': selectedUser.role=='rektorat',
							'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-300': selectedUser.role=='unit_kerja'
						}" x-text="selectedUser.role"></span>
					</div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Status</div>
					<div class="col-span-2">
						<span class="px-2 py-0.5 rounded-lg text-[11px] font-semibold" :class="selectedUser.status=='Aktif' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300' : 'bg-gray-200 text-gray-700 dark:bg-gray-600/40 dark:text-gray-300'" x-text="selectedUser.status"></span>
					</div>
					<div class="text-gray-500 dark:text-gray-400 text-xs">Terakhir Masuk</div>
					<div class="col-span-2 text-gray-700 dark:text-gray-300">-</div>
				</div>
			</div>
			<div class="flex items-center justify-end pt-2">
				<button type="button" @click="showView=false" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
