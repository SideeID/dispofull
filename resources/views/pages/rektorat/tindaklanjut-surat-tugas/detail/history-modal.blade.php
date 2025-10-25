<template x-if="showHistory"><template x-if="showHistory">

	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0" x-data="historyModal()">	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">

		<div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeAll()"></div>		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>

		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-slate-800 rounded-2xl shadow-xl ring-1 ring-slate-200 dark:ring-slate-700 p-6 flex flex-col gap-5 max-h-[90vh]">		<div class="relative w-full sm:max-w-xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5">

			<div class="flex items-start justify-between gap-4">			<div class="flex items-start justify-between gap-4">

				<div>				<div>

					<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Riwayat Tindak Lanjut</h3>					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Riwayat Surat Tugas</h3>

					<p class="text-sm text-slate-500 dark:text-slate-400 mt-1" x-text="selected?.subject"></p>					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || selected?.temp"></p>

					<p class="text-xs text-slate-400 dark:text-slate-500" x-text="selected?.letter_number || 'DRAFT-' + selected?.id"></p>				</div>

				</div>				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>

				<button @click="closeAll()" class="text-slate-400 hover:text-rose-600">			</div>

					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">			<div class="max-h-[55vh] overflow-y-auto pr-1 text-sm">

						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>				<ul class="space-y-4" x-data="{ logs: [] }" x-init="logs = [

					</svg>					{time:'2025-10-02 09:40',actor:'Sekretariat',action:'Menambahkan peserta baru',status:'success'},

				</button>					{time:'2025-10-02 09:30',actor:'Sekretariat',action:'Mengubah prioritas menjadi high',status:'info'},

			</div>					{time:'2025-10-02 09:25',actor:'Sekretariat',action:'Mengunggah lampiran tor_kegiatan.pdf',status:'success'},

					{time:'2025-10-02 09:12',actor:'Sekretariat',action:'Membuat draft surat tugas',status:'success'},

			<!-- Summary Stats -->				]">

			<div class="grid grid-cols-4 gap-3 bg-slate-50 dark:bg-slate-700/40 rounded-lg p-4">					<template x-for="l in logs" :key="l.time + l.action">

				<div class="text-center">						<li class="flex items-start gap-3">

					<div class="text-xs text-slate-500 dark:text-slate-400">Total Penerima</div>							<div class="w-10 text-[11px] font-mono text-gray-400 dark:text-gray-500 mt-0.5" x-text="l.time.split(' ')[1]"></div>

					<div class="text-2xl font-bold text-slate-700 dark:text-slate-300" x-text="summary.total"></div>							<div class="flex-1">

				</div>								<div class="text-[11px] font-semibold" :class="{

				<div class="text-center border-l border-slate-200 dark:border-slate-600">									'text-emerald-600 dark:text-emerald-400': l.status=='success',

					<div class="text-xs text-green-600 dark:text-green-400">Selesai</div>									'text-amber-600 dark:text-amber-400': l.status=='warning',

					<div class="text-2xl font-bold text-green-700 dark:text-green-300" x-text="summary.completed"></div>									'text-rose-600 dark:text-rose-400': l.status=='error',

				</div>									'text-slate-500 dark:text-slate-400': l.status=='info'

				<div class="text-center border-l border-slate-200 dark:border-slate-600">								}" x-text="l.actor"></div>

					<div class="text-xs text-yellow-600 dark:text-yellow-400">Proses</div>								<div class="text-gray-600 dark:text-gray-300 leading-snug" x-text="l.action"></div>

					<div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300" x-text="summary.in_progress"></div>								<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="l.time"></div>

				</div>							</div>

				<div class="text-center border-l border-slate-200 dark:border-slate-600">						</li>

					<div class="text-xs text-slate-600 dark:text-slate-400">Pending</div>					</template>

					<div class="text-2xl font-bold text-slate-700 dark:text-slate-300" x-text="summary.pending"></div>				</ul>

				</div>			</div>

			</div>			<div class="flex items-center justify-end pt-2">

				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">Tutup</button>

			<!-- Loading State -->			</div>

			<template x-if="loading">		</div>

				<div class="text-center py-8">	</div>

					<svg class="animate-spin h-8 w-8 mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24"></template>

						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
				</div>
			</template>

			<!-- Timeline -->
			<template x-if="!loading">
				<div class="overflow-y-auto pr-2" style="max-height: 60vh;">
					<template x-if="timeline.length === 0">
						<div class="text-center py-8 text-slate-500">
							Belum ada aktivitas tindak lanjut
						</div>
					</template>

					<div class="relative pl-6 border-l-2 border-slate-200 dark:border-slate-700">
						<template x-for="(item, idx) in timeline" :key="idx">
							<div class="relative mb-6 pb-6 border-b border-slate-100 dark:border-slate-700/50 last:border-0">
								<!-- Timeline Dot -->
								<div class="absolute -left-[1.65rem] top-0 w-6 h-6 rounded-full border-2 flex items-center justify-center"
									:class="{
										'bg-green-100 border-green-500 dark:bg-green-900 dark:border-green-400': item.status === 'completed',
										'bg-yellow-100 border-yellow-500 dark:bg-yellow-900 dark:border-yellow-400': item.status === 'in_progress',
										'bg-slate-100 border-slate-400 dark:bg-slate-700 dark:border-slate-500': item.status === 'pending'
									}">
									<svg class="w-3 h-3" :class="{
										'text-green-600 dark:text-green-400': item.status === 'completed',
										'text-yellow-600 dark:text-yellow-400': item.status === 'in_progress',
										'text-slate-500': item.status === 'pending'
									}" fill="currentColor" viewBox="0 0 20 20">
										<circle cx="10" cy="10" r="3"></circle>
									</svg>
								</div>

								<!-- Content -->
								<div class="ml-2">
									<div class="flex items-start justify-between gap-2 mb-2">
										<div class="flex-1">
											<div class="flex items-center gap-2 mb-1">
												<span class="font-medium text-slate-800 dark:text-slate-100" x-text="item.recipient_name"></span>
												<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
													:class="{
														'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300': item.status === 'completed',
														'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300': item.status === 'in_progress',
														'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300': item.status === 'pending'
													}"
													x-text="getStatusLabel(item.status)">
												</span>
											</div>
											<div class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-2">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>
												<span x-text="formatDateTime(item.created_at)"></span>
											</div>
										</div>
									</div>

									<!-- Notes -->
									<div x-show="item.notes" class="mb-2">
										<div class="text-sm bg-slate-50 dark:bg-slate-700/40 rounded-lg p-3">
											<div class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Catatan:</div>
											<div class="text-slate-700 dark:text-slate-300" x-text="item.notes"></div>
										</div>
									</div>

									<!-- Response -->
									<div x-show="item.response" class="mb-2">
										<div class="text-sm bg-indigo-50 dark:bg-indigo-900/20 rounded-lg p-3 border-l-4 border-indigo-400">
											<div class="text-xs font-medium text-indigo-600 dark:text-indigo-400 mb-1 flex items-center gap-1">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
												</svg>
												Response:
											</div>
											<div class="text-slate-700 dark:text-slate-300" x-text="item.response"></div>
										</div>
									</div>

									<!-- Completed Info -->
									<div x-show="item.completed_at" class="flex items-center gap-2 text-xs text-green-600 dark:text-green-400">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
										</svg>
										<span>Selesai pada <span x-text="formatDateTime(item.completed_at)"></span></span>
									</div>

									<!-- Read Info -->
									<div x-show="item.read_at && !item.completed_at" class="flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400">
										<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
										</svg>
										<span>Dibaca pada <span x-text="formatDateTime(item.read_at)"></span></span>
									</div>
								</div>
							</div>
						</template>
					</div>
				</div>
			</template>

			<div class="flex items-center justify-end pt-2 border-t border-slate-200 dark:border-slate-700">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-slate-600 hover:bg-slate-700 text-white font-medium">
					Tutup
				</button>
			</div>
		</div>
	</div>
</template>

<script>
function historyModal() {
	return {
		loading: false,
		timeline: [],
		summary: {
			total: 0,
			completed: 0,
			in_progress: 0,
			pending: 0
		},

		init() {
			this.$watch('showHistory', (value) => {
				if (value && this.selected) {
					this.fetchHistory();
				}
			});
		},

		async fetchHistory() {
			if (!this.selected?.id) return;
			
			this.loading = true;
			try {
				const response = await fetch(`/rektor/api/tindak-lanjut/${this.selected.id}`);
				const data = await response.json();
				
				// Build timeline from recipients
				this.timeline = [];
				if (data.data.recipients) {
					data.data.recipients.forEach(recipient => {
						recipient.timeline.forEach(activity => {
							this.timeline.push({
								...activity,
								recipient_name: recipient.recipient_type === 'user' 
									? recipient.recipient?.name 
									: recipient.recipient?.name
							});
						});
					});
				}

				// Sort by date desc
				this.timeline.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

				// Update summary
				this.summary = data.data.stats;
			} catch (error) {
				console.error('Error fetching history:', error);
			} finally {
				this.loading = false;
			}
		},

		getStatusLabel(status) {
			const labels = {
				pending: 'Belum Dimulai',
				in_progress: 'Dalam Proses',
				completed: 'Selesai'
			};
			return labels[status] || status;
		},

		formatDateTime(date) {
			if (!date) return '-';
			return new Date(date).toLocaleString('id-ID', { 
				day: '2-digit', 
				month: 'short', 
				year: 'numeric',
				hour: '2-digit',
				minute: '2-digit'
			});
		}
	}
}
</script>
