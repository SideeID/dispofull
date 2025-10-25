<template x-if="showView">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Detail Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="grid md:grid-cols-2 gap-6 text-sm">
				<div class="space-y-3">
					<div>
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Perihal</div>
						<div class="text-gray-700 dark:text-gray-200 font-medium" x-text="selected?.subject"></div>
					</div>
					<div class="grid grid-cols-2 gap-4">
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Tujuan / Tim</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.destination"></div>
						</div>
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Tanggal Surat</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.date"></div>
						</div>
					</div>
					<div class="grid grid-cols-2 gap-4">
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Periode</div>
							<div class="text-gray-700 dark:text-gray-200" x-text="selected?.start + ' s/d ' + selected?.end"></div>
						</div>
						<div>
							<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Status</div>
							<div><span class="px-2 py-0.5 rounded-full text-[11px] font-medium" :class="{
								'bg-slate-500/10 text-slate-600 dark:text-slate-300': selected?.status=='draft',
								'bg-amber-500/10 text-amber-600 dark:text-amber-400': selected?.status=='pending',
								'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': selected?.status=='processed',
								'bg-rose-500/10 text-rose-600 dark:text-rose-400': selected?.status=='rejected',
								'bg-gray-500/10 text-gray-600 dark:text-gray-300': selected?.status=='closed',
								'bg-slate-500/10 text-slate-600 dark:text-slate-400': selected?.status=='archived',
							}" x-text="statusLabel(selected?.status)"></span></div>
						</div>
					</div>
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
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500">Ringkasan</div>
						<p class="text-gray-600 dark:text-gray-300 mt-1 leading-relaxed" x-text="selected?.notes?.ringkasan || '-' "></p>
					</div>
				</div>
				<div class="space-y-5">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Peserta</div>
						<template x-if="Array.isArray(selected?.participants) && selected.participants.length">
							<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300">
								<template x-for="(p,idx) in selected.participants" :key="idx">
									<li x-text="`${p.nama}${p.jabatan ? ' · ' + p.jabatan : ''}`"></li>
								</template>
								<li><button @click="closeAll(); open('showParticipants', selected)" class="text-amber-600 dark:text-amber-400 hover:underline">Kelola »</button></li>
							</ul>
						</template>
						<template x-if="!selected?.participants || !selected.participants.length">
							<div class="text-xs text-gray-400">Belum ada peserta. <button @click="closeAll(); open('showParticipants', selected)" class="text-amber-600 dark:text-amber-400 hover:underline">Tambah »</button></div>
						</template>
					</div>
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Aktivitas Terakhir</div>
						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300" x-data="{}" x-init="">
							<li class="text-gray-400">Kunjungi menu Riwayat untuk detail.</li>
						</ul>
					</div>
					<div class="flex gap-2" x-show="['draft','pending'].includes(selected?.status)">
						<button @click="closeAll(); open('showSign', selected)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Tanda Tangan</button>
						<button @click="closeAll(); open('showParticipants', selected)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Peserta</button>
						<button @click="closeAll(); open('showConfirm', selected)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-amber-600 hover:bg-amber-500 text-white font-medium">Konfirmasi</button>
					</div>
					<div class="flex gap-2" x-show="selected">
						<button @click="closeAll(); open('showChangeStatus', selected)" class="flex-1 px-4 py-2 rounded-lg text-xs bg-indigo-600 hover:bg-indigo-500 text-white font-medium">Ubah Status</button>
					</div>
				</div>
			</div>
			<div class="flex items-center justify-between pt-2">
				<button type="button" @click="closeAll(); open('showPreview', selected)" class="px-4 py-2 rounded-lg text-sm bg-indigo-600 hover:bg-indigo-500 text-white font-medium flex items-center gap-2">
					<i data-feather="eye" class="w-4 h-4"></i> Preview Surat
				</button>
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">Tutup</button>
			</div>
		</div>
	</div>
</template>
