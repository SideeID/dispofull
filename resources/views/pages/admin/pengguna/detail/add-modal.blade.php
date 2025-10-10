<template x-if="showAdd">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="showAdd=false"></div>
		<div class="relative w-full sm:max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tambah Pengguna</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Isi data untuk membuat akun baru.</p>
				</div>
				<button @click="showAdd=false" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-4" @submit.prevent="storeUser()">
				<div class="grid md:grid-cols-2 gap-4">
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Nama Lengkap</label>
						<input type="text" x-model="formAdd.name" required class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Nama" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Email</label>
						<input type="email" x-model="formAdd.email" required class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="email@domain.ac.id" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">NIP</label>
						<input type="text" x-model="formAdd.nip" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Nomor Induk" />
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Departemen / Unit</label>
						<select x-model="formAdd.department_id" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="">-- Pilih --</option>
							<template x-for="d in departments" :key="d.id">
								<option :value="d.id" x-text="d.name + ' (' + d.code + ')' "></option>
							</template>
						</select>
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Role</label>
						<select x-model="formAdd.role" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="admin">Admin</option>
							<option value="rektorat">Rektorat</option>
							<option value="unit_kerja">Unit Kerja</option>
						</select>
					</div>
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Status</label>
						<select x-model="formAdd.status" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
							<option value="active">Aktif</option>
							<option value="inactive">Nonaktif</option>
						</select>
					</div>
				</div>
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Password</label>
					<input type="password" x-model="formAdd.password" required class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Minimal 8 karakter" />
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="showAdd=false" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
						Simpan
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
