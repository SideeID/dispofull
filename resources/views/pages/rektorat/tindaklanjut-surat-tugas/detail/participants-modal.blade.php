<template x-if="showParticipants"><template x-if="showParticipants">

	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0" x-data="participantsModal()">	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">

		<div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="closeAll()"></div>		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>

		<div class="relative w-full sm:max-w-4xl bg-white dark:bg-slate-800 rounded-2xl shadow-xl ring-1 ring-slate-200 dark:ring-slate-700 p-6 flex flex-col gap-6 max-h-[90vh] overflow-y-auto">		<div class="relative w-full sm:max-w-2xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6">

			<div class="flex items-start justify-between gap-4">			<div class="flex items-start justify-between gap-4">

				<div>				<div>

					<h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Monitoring Tindak Lanjut</h3>					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Peserta Surat Tugas</h3>

					<p class="text-sm text-slate-500 dark:text-slate-400 mt-1">					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || selected?.temp"></p>

						<span x-text="selected?.subject"></span>				</div>

					</p>				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>

					<p class="text-xs text-slate-400 dark:text-slate-500" x-text="selected?.letter_number || 'DRAFT-' + selected?.id"></p>			</div>

				</div>			<div class="grid md:grid-cols-2 gap-6 text-sm">

				<button @click="closeAll()" class="text-slate-400 hover:text-rose-600">				<div class="space-y-4">

					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4 h-full">

						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-3">Daftar Peserta</div>

					</svg>						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-56 overflow-y-auto pr-1">

				</button>							<li class="flex items-center justify-between gap-2"><span>Dr. Andi · Ketua</span><button class="text-rose-500 hover:underline">Hapus</button></li>

			</div>							<li class="flex items-center justify-between gap-2"><span>Ir. Budi · Anggota</span><button class="text-rose-500 hover:underline">Hapus</button></li>

							<li class="flex items-center justify-between gap-2"><span>Dr. Citra · Anggota</span><button class="text-rose-500 hover:underline">Hapus</button></li>

			<!-- Stats Progress -->							<li class="flex items-center justify-between gap-2"><span>Sari, M.Sc · Anggota</span><button class="text-rose-500 hover:underline">Hapus</button></li>

			<div class="grid grid-cols-3 gap-4">						</ul>

				<div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">					</div>

					<div class="text-xs text-green-600 dark:text-green-400 font-medium mb-1">Selesai</div>				</div>

					<div class="text-2xl font-bold text-green-700 dark:text-green-300" x-text="recipients.filter(r => r.status === 'completed').length"></div>				<div class="space-y-5">

				</div>					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">

				<div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4">						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Tambah Peserta</div>

					<div class="text-xs text-yellow-600 dark:text-yellow-400 font-medium mb-1">Dalam Proses</div>						<form class="space-y-4" @submit.prevent="alert('Tambah peserta (dummy)')">

					<div class="text-2xl font-bold text-yellow-700 dark:text-yellow-300" x-text="recipients.filter(r => r.status === 'in_progress').length"></div>							<div>

				</div>								<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Nama</label>

				<div class="bg-slate-50 dark:bg-slate-700/40 rounded-lg p-4">								<input type="text" class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500"  />

					<div class="text-xs text-slate-600 dark:text-slate-400 font-medium mb-1">Belum Dimulai</div>							</div>

					<div class="text-2xl font-bold text-slate-700 dark:text-slate-300" x-text="recipients.filter(r => r.status === 'pending').length"></div>							<div class="grid grid-cols-2 gap-4">

				</div>								<div>

			</div>									<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Peran</label>

									<select class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500">

			<!-- Loading State -->										<option value="anggota">Anggota</option>

			<template x-if="loading">										<option value="ketua">Ketua</option>

				<div class="text-center py-8">									</select>

					<svg class="animate-spin h-8 w-8 mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24">								</div>

						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>								<div>

						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>									<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Unit</label>

					</svg>									<select class="w-full rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500"><option value="">Pilih Unit/Departemen</option><template x-for="dept in availableDepartments" :key="dept.id"><option :value="dept.name" x-text="dept.name"></option></template></select>

				</div>								</div>

			</template>							</div>

							<div class="flex items-center justify-end">

			<!-- Recipients List -->								<button type="submit" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Tambah</button>

			<template x-if="!loading">							</div>

				<div class="space-y-3">						</form>

					<div class="text-sm font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-200 dark:border-slate-700 pb-2">					</div>

						Daftar Penerima Disposisi (<span x-text="recipients.length"></span>)					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">

					</div>						<div class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">Log Perubahan Peserta</div>

						<ul class="space-y-2 text-xs text-gray-600 dark:text-gray-300 max-h-28 overflow-y-auto pr-1">

					<template x-if="recipients.length === 0">							<li>2025-10-02 09:40 · Menambah Sari, M.Sc</li>

						<div class="text-center py-8 text-slate-500">							<li>2025-10-02 09:35 · Mengubah peran Budi menjadi Anggota</li>

							Belum ada disposisi untuk surat tugas ini						</ul>

						</div>					</div>

					</template>				</div>

			</div>

					<template x-for="(recipient, idx) in recipients" :key="idx">			<div class="flex items-center justify-end pt-2">

						<div class="bg-slate-50 dark:bg-slate-700/40 rounded-lg p-4 hover:bg-slate-100 dark:hover:bg-slate-700 transition">				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">Tutup</button>

							<div class="flex items-start justify-between gap-4">			</div>

								<div class="flex-1">		</div>

									<div class="flex items-center gap-3 mb-2">	</div>

										<div class="flex items-center gap-2"></template>

											<svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
											</svg>
											<span class="font-medium text-slate-800 dark:text-slate-100" x-text="recipient.recipient_type === 'user' ? recipient.recipient?.name : recipient.recipient?.name"></span>
										</div>
										<span :class="getStatusBadgeClass(recipient.status)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" x-text="getStatusLabel(recipient.status)"></span>
									</div>

									<div class="grid grid-cols-2 gap-4 text-sm mb-3">
										<div>
											<span class="text-slate-500 dark:text-slate-400">Tipe:</span>
											<span class="font-medium text-slate-700 dark:text-slate-300 ml-1" x-text="recipient.recipient_type === 'user' ? 'User' : 'Department'"></span>
										</div>
										<div x-show="recipient.completed_at">
											<span class="text-slate-500 dark:text-slate-400">Selesai:</span>
											<span class="font-medium text-slate-700 dark:text-slate-300 ml-1" x-text="formatDate(recipient.completed_at)"></span>
										</div>
									</div>

									<!-- Response -->
									<div x-show="recipient.response" class="bg-white dark:bg-slate-800 rounded-lg p-3 border border-slate-200 dark:border-slate-600">
										<div class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Response:</div>
										<div class="text-sm text-slate-700 dark:text-slate-300" x-text="recipient.response"></div>
									</div>

									<!-- Timeline Toggle -->
									<button @click="toggleTimeline(idx)" class="mt-3 text-xs text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1">
										<svg class="w-4 h-4" :class="showTimeline === idx && 'rotate-90'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
										</svg>
										<span x-text="showTimeline === idx ? 'Sembunyikan Timeline' : 'Lihat Timeline (' + recipient.timeline.length + ' aktivitas)'"></span>
									</button>

									<!-- Timeline Detail -->
									<div x-show="showTimeline === idx" x-collapse class="mt-3 pl-4 border-l-2 border-indigo-200 dark:border-indigo-800 space-y-3">
										<template x-for="(activity, actIdx) in recipient.timeline" :key="actIdx">
											<div class="relative pl-6">
												<div class="absolute left-0 top-1 w-3 h-3 rounded-full" :class="activity.status === 'completed' ? 'bg-green-500' : activity.status === 'in_progress' ? 'bg-yellow-500' : 'bg-slate-300'"></div>
												<div class="text-xs text-slate-500 dark:text-slate-400" x-text="formatDateTime(activity.created_at)"></div>
												<div class="text-sm font-medium text-slate-700 dark:text-slate-300 mt-1" x-text="getStatusLabel(activity.status)"></div>
												<div x-show="activity.notes" class="text-sm text-slate-600 dark:text-slate-400 mt-1" x-text="activity.notes"></div>
												<div x-show="activity.response" class="text-sm bg-indigo-50 dark:bg-indigo-900/20 rounded p-2 mt-2">
													<span class="text-xs font-medium text-indigo-600 dark:text-indigo-400">Response: </span>
													<span class="text-slate-700 dark:text-slate-300" x-text="activity.response"></span>
												</div>
											</div>
										</template>
									</div>
								</div>

								<!-- Progress Circle -->
								<div class="flex flex-col items-center gap-1">
									<div class="relative w-16 h-16">
										<svg class="w-16 h-16 transform -rotate-90">
											<circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" class="text-slate-200 dark:text-slate-700"></circle>
											<circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="none" 
												:class="recipient.status === 'completed' ? 'text-green-500' : recipient.status === 'in_progress' ? 'text-yellow-500' : 'text-slate-300'"
												:stroke-dasharray="175.93"
												:stroke-dashoffset="recipient.status === 'completed' ? 0 : recipient.status === 'in_progress' ? 87.96 : 175.93"
												stroke-linecap="round">
											</circle>
										</svg>
										<div class="absolute inset-0 flex items-center justify-center">
											<span class="text-xs font-bold" x-text="recipient.status === 'completed' ? '100%' : recipient.status === 'in_progress' ? '50%' : '0%'"></span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</template>
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
function participantsModal() {
	return {
		loading: false,
		recipients: [],
		showTimeline: null,

		init() {
			this.$watch('showParticipants', (value) => {
				if (value && this.selected) {
					this.fetchRecipients();
				}
			});
		},

		async fetchRecipients() {
			if (!this.selected?.id) return;
			
			this.loading = true;
			try {
				const response = await fetch(`/rektor/api/tindak-lanjut/${this.selected.id}`);
				const data = await response.json();
				this.recipients = data.data.recipients || [];
			} catch (error) {
				console.error('Error fetching recipients:', error);
			} finally {
				this.loading = false;
			}
		},

		toggleTimeline(idx) {
			this.showTimeline = this.showTimeline === idx ? null : idx;
		},

		getStatusBadgeClass(status) {
			const classes = {
				pending: 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
				in_progress: 'bg-yellow-100 dark:bg-yellow-900 text-yellow-700 dark:text-yellow-300',
				completed: 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
			};
			return classes[status] || classes.pending;
		},

		getStatusLabel(status) {
			const labels = {
				pending: 'Belum Dimulai',
				in_progress: 'Dalam Proses',
				completed: 'Selesai'
			};
			return labels[status] || status;
		},

		formatDate(date) {
			if (!date) return '-';
			return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
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






