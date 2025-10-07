<template x-if="showEdit && selectedUser">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0" x-data="{ form: {...selectedUser} }">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showEdit=false"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Edit Pengguna</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="form.name"></p>
				</div>
				<button @click="showEdit=false" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-4" @submit.prevent="alert('Submit edit pengguna (dummy)'); showEdit=false">
				<div class="grid md:grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Nama Lengkap</label>
						<input type="text" x-model="form.name" required class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Email</label>
						<input type="email" x-model="form.email" required class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">NIP</label>
						<input type="text" x-model="form.nip" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Departemen / Unit</label>
						<input type="text" x-model="form.department" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Role</label>
						<select x-model="form.role" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="admin">Admin</option>
							<option value="rektorat">Rektorat</option>
							<option value="unit_kerja">Unit Kerja</option>
						</select>
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
						<select x-model="form.status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="Aktif">Aktif</option>
							<option value="Nonaktif">Nonaktif</option>
						</select>
					</div>
				</div>
				<div class="grid md:grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Password (opsional)</label>
						<input type="password" x-ref="pwd" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Biarkan kosong jika tidak ganti" />
					</div>
					<div class="flex items-end">
						<button type="button" @click="$refs.pwd.value=''; $refs.pwd.type='password'" class="mt-4 px-3 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Reset Field</button>
					</div>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="showEdit=false" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
						<i data-feather='save' class='w-4 h-4'></i> Simpan Perubahan
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
