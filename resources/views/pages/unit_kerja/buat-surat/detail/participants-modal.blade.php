<template x-if="showParticipants">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Kelola Peserta / Penerima</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Peserta internal yang terkait dengan surat tugas.</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-500"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="flex-1 overflow-y-auto px-6 py-6 space-y-6 text-sm">
				<div class="grid md:grid-cols-3 gap-6">
					<div class="md:col-span-2 space-y-4">
						<div class="flex items-center gap-2">
							<div class="relative flex-1">
								<i data-feather='search' class='w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400'></i>
								<input x-model="participantSearch" type="text" placeholder="Cari nama / NIP" class="w-full pl-9 rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
							</div>
							<button type="button" @click="addParticipantManual()" class="px-3 py-2 rounded-lg bg-violet-600 hover:bg-violet-500 text-white text-[11px] flex items-center gap-1"><i data-feather='plus-circle' class='w-3.5 h-3.5'></i> Manual</button>
						</div>
						<div class="border border-gray-200 dark:border-gray-600 rounded-lg divide-y divide-gray-200 dark:divide-gray-600 bg-gray-50 dark:bg-gray-700/40" x-data="{ filtered(){ return participants.filter(p=> !participantSearch || p.nama.toLowerCase().includes(participantSearch.toLowerCase()) || p.nip.includes(participantSearch)); } }">
							<template x-for="p in filtered()" :key="p.nip">
								<div class="flex items-center gap-4 px-4 py-2 group">
									<div class="flex-1 min-w-0">
										<div class="flex items-center gap-2">
											<span class="text-xs font-semibold text-gray-700 dark:text-gray-200" x-text="p.nama"></span>
											<span class="text-[10px] px-1.5 py-0.5 rounded bg-indigo-500/10 text-indigo-600 dark:text-indigo-400" x-text="p.status"></span>
										</div>
										<div class="text-[11px] text-gray-500 dark:text-gray-400 font-mono" x-text="p.nip"></div>
										<div class="text-[11px] text-gray-400 dark:text-gray-500" x-text="p.jabatan"></div>
									</div>
									<div class="flex items-center gap-1">
										<button type="button" @click="toggleStatus(p)" class="p-1.5 rounded bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-600 text-gray-500 hover:text-violet-600"><i data-feather='refresh-ccw' class='w-3.5 h-3.5'></i></button>
										<button type="button" @click="removeParticipant(p)" class="p-1.5 rounded bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-600 text-rose-500 hover:text-rose-600"><i data-feather='trash-2' class='w-3.5 h-3.5'></i></button>
									</div>
								</div>
							</template>
							<div class="px-4 py-6 text-center text-[11px] text-gray-400" x-show="filtered().length===0">Tidak ada peserta.</div>
						</div>
					</div>
					<div class="space-y-5">
						<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 space-y-4">
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Statistik</div>
							<div class="grid grid-cols-3 gap-3 text-center">
								<div class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
									<div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Total</div>
									<div class="text-lg font-semibold text-violet-600 dark:text-violet-400" x-text="participants.length"></div>
								</div>
								<div class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
									<div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Aktif</div>
									<div class="text-lg font-semibold text-emerald-600 dark:text-emerald-400" x-text="participants.filter(p=>p.status==='aktif').length"></div>
								</div>
								<div class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600">
									<div class="text-[10px] uppercase tracking-wide text-gray-400 dark:text-gray-500">Nonaktif</div>
									<div class="text-lg font-semibold text-rose-600 dark:text-rose-400" x-text="participants.filter(p=>p.status!=='aktif').length"></div>
								</div>
							</div>
							<div class="space-y-2">
								<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Tambah Manual</div>
								<form @submit.prevent="addParticipantManual(true)" class="space-y-2 text-xs">
									<input x-model="manual.nama" type="text" placeholder="Nama" class="w-full rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
									<input x-model="manual.nip" type="text" placeholder="NIP" class="w-full rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
									<input x-model="manual.jabatan" type="text" placeholder="Jabatan" class="w-full rounded bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-400 dark:focus:ring-violet-500" />
									<div class="flex justify-end"><button type="submit" class="px-3 py-1.5 rounded bg-violet-600 hover:bg-violet-500 text-white text-[11px]">Tambah</button></div>
								</form>
							</div>
						</div>
						<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 space-y-2">
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Catatan</div>
							<p class="text-[11px] text-gray-500 dark:text-gray-400 leading-snug">Peserta akan menerima notifikasi setelah surat disetujui dan telah ditandatangani secara digital.</p>
						</div>
					</div>
				</div>
			</div>
			<div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-end gap-3 bg-white dark:bg-gray-800 rounded-b-2xl">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">Tutup</button>
			</div>
		</div>
	</div>
</template>
