<template x-if="showCreate">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6 overflow-y-auto max-h-[90vh]">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Buat Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Isi data untuk draft surat tugas baru.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<form class="space-y-6 text-sm" @submit.prevent="submitCreate()">
				<div class="grid md:grid-cols-3 gap-5">
					<div class="md:col-span-2 space-y-5">
						<div class="grid md:grid-cols-2 gap-4">
							<div>
								<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Perihal</label>
								<input type="text" x-model="createForm.subject" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Judul / tujuan surat tugas" />
							</div>
							<div>
								<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tanggal Surat</label>
								<input type="date" x-model="createForm.letter_date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
							</div>
							<div>
								<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Mulai</label>
								<input type="date" x-model="createForm.notes.start_date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
							</div>
							<div>
								<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Periode Selesai</label>
								<input type="date" x-model="createForm.notes.end_date" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" />
							</div>
						</div>
						<div>
							<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Tujuan / Tim</label>
							<input type="text" x-model="createForm.notes.destination" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Nama tim / unit" />
						</div>
						<div>
							<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Prioritas</label>
							<select x-model="createForm.priority" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100">
								<option value="normal">Normal</option>
								<option value="high">High</option>
								<option value="urgent">Urgent</option>
								<option value="low">Low</option>
							</select>
						</div>
						<div>
							<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan</label>
							<textarea rows="4" x-model="createForm.notes.ringkasan" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100" placeholder="Ringkasan / tujuan kegiatan ..."></textarea>
						</div>
					</div>
					<div class="space-y-5">
						<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Peserta (Draft)</div>
							<template x-if="createForm.notes.participants.length">
								<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
									<template x-for="(p,idx) in createForm.notes.participants" :key="idx">
										<li class="flex items-center justify-between">
											<span x-text="p.nama"></span>
											<button type="button" class="text-rose-500 hover:underline" @click="createForm.notes.participants.splice(idx,1)">Hapus</button>
										</li>
									</template>
								</ul>
							</template>
							<div class="mt-3">
								<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Tambah Peserta</label>
								<div class="flex gap-2">
									<input type="text" x-model="newParticipant.nama" class="flex-1 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500" placeholder="Nama" />
									<button type="button" @click="if(newParticipant.nama){ createForm.notes.participants.push(newParticipant); newParticipant={nama:'',nip:'',jabatan:'anggota',status:''}; }" class="px-3 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium">Tambah</button>
								</div>
							</div>
						</div>
						<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Lampiran</div>
							<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
								<li class="flex items-center justify-between gap-3 p-2 rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600"><span>tor_kegiatan.pdf</span><button class="text-rose-500 hover:underline">Hapus</button></li>
							</ul>
							<div class="mt-3">
								<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Tambah Lampiran</label>
								<input type="file" class="block w-full text-[11px] text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-500/10 dark:file:text-amber-300" />
								<p class="mt-1 text-[10px] text-gray-400">PDF / Gambar (max 5MB)</p>
							</div>
						</div>
					</div>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="submit" :disabled="creating" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
						<span x-show="!creating">Simpan Draft</span>
						<span x-show="creating">Menyimpan...</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
