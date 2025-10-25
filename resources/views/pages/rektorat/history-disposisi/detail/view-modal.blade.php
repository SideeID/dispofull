<template x-if="showView">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-4xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6 max-h-[90vh] overflow-y-auto">
			<div class="flex items-start justify-between gap-4 sticky top-0 bg-white dark:bg-gray-800 pb-4 border-b border-gray-200 dark:border-gray-700 z-10">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Disposisi</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>

			<div class="grid md:grid-cols-2 gap-6 text-sm">
				<!-- Left Column: Surat Info -->
				<div class="space-y-4">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 space-y-3">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
							<i data-feather="file-text" class="w-4 h-4 text-violet-600 dark:text-violet-400"></i>
							Informasi Surat
						</h4>
						<div class="grid gap-3">
							<div>
								<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Perihal</div>
								<div class="text-gray-700 dark:text-gray-200 font-medium" x-text="selected?.subject"></div>
							</div>
							<div class="grid grid-cols-2 gap-4">
								<div>
									<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Asal Surat</div>
									<div class="text-gray-700 dark:text-gray-200" x-text="selected?.origin"></div>
								</div>
								<div>
									<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Tanggal</div>
									<div class="text-gray-700 dark:text-gray-200" x-text="selected?.date"></div>
								</div>
							</div>
							<div class="grid grid-cols-2 gap-4">
								<div>
									<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Prioritas</div>
									<div><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="{
										'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': selected?.priority=='low',
										'bg-slate-500/10 text-slate-600 dark:text-slate-300': selected?.priority=='normal',
										'bg-amber-500/10 text-amber-600 dark:text-amber-400': selected?.priority=='high',
										'bg-rose-500/10 text-rose-600 dark:text-rose-400': selected?.priority=='urgent'
									}" x-text="selected?.priority"></span></div>
								</div>
								<div>
									<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Status</div>
									<div><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="statusClass(normalizeStatus(selected||{}))" x-text="normalizeStatus(selected||{})"></span></div>
								</div>
							</div>
						</div>
					</div>

					<div class="bg-violet-50 dark:bg-violet-900/20 rounded-lg p-4 space-y-3">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
							<i data-feather="bar-chart-2" class="w-4 h-4 text-violet-600 dark:text-violet-400"></i>
							Statistik Disposisi
						</h4>
						<div class="grid grid-cols-2 gap-4 text-xs">
							<div>
								<div class="text-gray-500 dark:text-gray-400">Total Langkah</div>
								<div class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="selected?.chain || 0"></div>
							</div>
							<div>
								<div class="text-gray-500 dark:text-gray-400">Lampiran</div>
								<div class="text-lg font-bold text-gray-800 dark:text-gray-100" x-text="selected?.attachments || 0"></div>
							</div>
						</div>
						<div>
							<div class="text-gray-500 dark:text-gray-400">Tujuan Akhir</div>
							<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="selected?.to"></div>
						</div>
					</div>
				</div>

				<!-- Right Column: Timeline & Tracking -->
				<div class="space-y-4">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
							<i data-feather="activity" class="w-4 h-4 text-amber-600 dark:text-amber-400"></i>
							Timeline Aktivitas
						</h4>
						<div class="space-y-3 max-h-60 overflow-y-auto">
							<template x-for="(log, idx) in logs.slice(0, 5)" :key="idx">
								<div class="flex gap-3 text-xs">
									<div class="flex flex-col items-center">
										<div class="w-2 h-2 rounded-full bg-violet-500"></div>
										<div class="w-0.5 flex-1 bg-gray-200 dark:bg-gray-600 mt-1" x-show="idx < logs.slice(0, 5).length - 1"></div>
									</div>
									<div class="flex-1 pb-3">
										<div class="text-gray-500 dark:text-gray-400" x-text="log.time"></div>
										<div class="text-gray-800 dark:text-gray-100 font-medium" x-text="log.actor"></div>
										<div class="text-gray-600 dark:text-gray-300" x-text="log.action"></div>
									</div>
								</div>
							</template>
							<div x-show="!logs || logs.length === 0" class="text-xs text-gray-400 text-center py-4">
								Belum ada aktivitas
							</div>
						</div>
					</div>

					<div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-4">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
							<i data-feather="users" class="w-4 h-4 text-emerald-600 dark:text-emerald-400"></i>
							Status Penerima
						</h4>
						<div class="space-y-2">
							<template x-for="(log, idx) in logs.filter(l => l.action.includes('disposisi') || l.action.includes('Membaca') || l.action.includes('selesai'))" :key="'status-' + idx">
								<div class="flex items-center justify-between text-xs p-2 rounded bg-white dark:bg-gray-700/60">
									<div class="flex items-center gap-2">
										<i data-feather="user" class="w-3 h-3 text-gray-400"></i>
										<span class="text-gray-800 dark:text-gray-100 font-medium" x-text="log.actor"></span>
									</div>
									<div>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-medium" :class="{
											'bg-blue-500/10 text-blue-600': log.action.includes('Membaca'),
											'bg-emerald-500/10 text-emerald-600': log.action.includes('selesai'),
											'bg-amber-500/10 text-amber-600': log.action.includes('disposisi')
										}" x-text="log.action.includes('selesai') ? 'Selesai' : log.action.includes('Membaca') ? 'Dibaca' : 'Diteruskan'"></span>
									</div>
								</div>
							</template>
							<div x-show="!logs || logs.filter(l => l.action.includes('disposisi') || l.action.includes('Membaca') || l.action.includes('selesai')).length === 0" class="text-xs text-gray-400 text-center py-2">
								Belum ada penerima
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Action Buttons -->
			<div class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
				<div class="flex gap-2">
					<button @click="closeAll(); showRoute=true; await loadRoute();" class="px-4 py-2 rounded-lg text-xs bg-violet-600 hover:bg-violet-500 text-white font-medium flex items-center gap-2">
						<i data-feather="git-commit" class="w-3.5 h-3.5"></i>
						Lihat Alur Lengkap
					</button>
					<button @click="closeAll(); showNotes=true; await loadNotes();" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center gap-2">
						<i data-feather="message-circle" class="w-3.5 h-3.5"></i>
						Catatan
					</button>
					<button @click="closeAll(); showAttachments=true; await loadAttachments();" x-show="selected?.attachments>0" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 flex items-center gap-2">
						<i data-feather="paperclip" class="w-3.5 h-3.5"></i>
						Lampiran
					</button>
				</div>
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Tutup</button>
			</div>
		</div>
	</div>
</template>
