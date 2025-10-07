<template x-if="showDelete && selectedUser">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showDelete=false"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Konfirmasi Hapus</h3>
					<p class="text-xs text-rose-600 dark:text-rose-400 mt-1">Aksi ini tidak dapat dibatalkan.</p>
				</div>
				<button @click="showDelete=false" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<p class="text-sm text-gray-600 dark:text-gray-300">Anda yakin ingin menghapus pengguna <span class="font-semibold" x-text="selectedUser.name"></span>? Akses dan data terkait mungkin terpengaruh.</p>
			<div class="flex items-center justify-end gap-3 pt-2">
				<button type="button" @click="showDelete=false" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
				<button type="button" @click="deleteUser()" class="px-4 py-2 rounded-lg text-sm bg-rose-600 hover:bg-rose-500 text-white font-medium flex items-center gap-2">
					<i data-feather='trash' class='w-4 h-4'></i> Hapus
				</button>
			</div>
		</div>
	</div>
</template>
