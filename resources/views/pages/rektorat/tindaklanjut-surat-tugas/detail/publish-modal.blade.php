<template x-if="showPublish">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Publish Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-5 text-sm" @submit.prevent="alert('Publish surat tugas (dummy)'); closeAll()">
				<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 space-y-4">
					<p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Publikasi akan mengubah status menjadi <span class="font-semibold text-emerald-600 dark:text-emerald-400">published</span> dan mendistribusikan notifikasi ke peserta.</p>
					<div class="grid grid-cols-2 gap-4">
						<div>
							<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Publish</label>
							<input type="date" class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500" />
						</div>
						<div>
							<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Distribusi</label>
							<select class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500">
								<option value="email">Email</option>
								<option value="system">Sistem</option>
								<option value="both">Email + Sistem</option>
							</select>
						</div>
					</div>
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan (opsional)</label>
						<textarea rows="3" class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500" placeholder="Catatan publikasi..."></textarea>
					</div>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Publish</button>
				</div>
			</form>
		</div>
	</div>
</template>
