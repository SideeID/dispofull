<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="tindakLanjutSuratTugas()"
		 @keydown.escape.window="closeAll()"
	>
		<!-- Header with Stats Cards -->
		<div class="mb-6">
			<h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 mb-6">Tindak Lanjut Surat Tugas</h1>
			
			<!-- Stats Cards -->
			<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
				<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900">
							<svg class="w-6 h-6 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm text-slate-500 dark:text-slate-400">Total Surat Tugas</p>
							<p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.total"></p>
						</div>
					</div>
				</div>

				<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-green-100 dark:bg-green-900">
							<svg class="w-6 h-6 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm text-slate-500 dark:text-slate-400">Selesai 100%</p>
							<p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.completed"></p>
						</div>
					</div>
				</div>

				<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-yellow-100 dark:bg-yellow-900">
							<svg class="w-6 h-6 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm text-slate-500 dark:text-slate-400">Dalam Proses</p>
							<p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.in_progress"></p>
						</div>
					</div>
				</div>

				<div class="bg-white dark:bg-slate-800 rounded-lg shadow p-4">
					<div class="flex items-center">
						<div class="p-3 rounded-full bg-red-100 dark:bg-red-900">
							<svg class="w-6 h-6 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
							</svg>
						</div>
						<div class="ml-4">
							<p class="text-sm text-slate-500 dark:text-slate-400">Belum Dimulai</p>
							<p class="text-2xl font-bold text-slate-900 dark:text-white" x-text="stats.pending"></p>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Filters -->
		<div class="bg-white dark:bg-slate-800 shadow rounded-sm mb-5">
			<div class="p-4">
				<form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-3 gap-4">
					<div>
						<label class="block text-sm font-medium mb-1">Cari</label>
						<input type="text" x-model="filters.q" placeholder="Nomor / Perihal" class="form-input w-full">
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Tanggal Surat</label>
						<input type="date" x-model="filters.date" class="form-input w-full">
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Status</label>
						<select x-model="filters.status" class="form-select w-full">
							<option value="">Semua Status</option>
							<option value="draft">Draft</option>
							<option value="need_signature">Perlu Tanda Tangan</option>
							<option value="signed">Sudah Ditandatangani</option>
							<option value="published">Dipublikasikan</option>
							<option value="archived">Diarsipkan</option>
						</select>
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Prioritas</label>
						<select x-model="filters.priority" class="form-select w-full">
							<option value="">Semua Prioritas</option>
							<option value="low">Rendah</option>
							<option value="normal">Normal</option>
							<option value="high">Tinggi</option>
							<option value="urgent">Mendesak</option>
						</select>
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Periode Mulai</label>
						<input type="date" x-model="filters.period_from" class="form-input w-full">
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Periode Selesai</label>
						<input type="date" x-model="filters.period_to" class="form-input w-full">
					</div>

					<div class="md:col-span-3 flex gap-2">
						<button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
							</svg>
							Filter
						</button>
						<button type="button" @click="resetFilters" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600 dark:text-slate-300">
							Reset
						</button>
					</div>
				</form>
			</div>
		</div>

		<!-- Table -->
		<div class="bg-white dark:bg-slate-800 shadow-lg rounded-sm border border-slate-200 dark:border-slate-700">
			<div class="p-3">
				<div class="overflow-x-auto">
					<table class="table-auto w-full dark:text-slate-300">
						<thead class="text-xs uppercase text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-700/20 rounded-sm">
							<tr>
								<th class="p-2 whitespace-nowrap w-32"><div class="font-semibold text-left">No/Draft</div></th>
								<th class="p-2 whitespace-nowrap"><div class="font-semibold text-left">Perihal</div></th>
								<th class="p-2 whitespace-nowrap w-40"><div class="font-semibold text-left">Tujuan/Tim</div></th>
								<th class="p-2 whitespace-nowrap w-32"><div class="font-semibold text-left">Periode</div></th>
								<th class="p-2 whitespace-nowrap w-24"><div class="font-semibold text-center">Prioritas</div></th>
								<th class="p-2 whitespace-nowrap w-28"><div class="font-semibold text-center">Status</div></th>
								<th class="p-2 whitespace-nowrap w-24"><div class="font-semibold text-center">Progress</div></th>
								<th class="p-2 whitespace-nowrap w-20"><div class="font-semibold text-center">Peserta</div></th>
								<th class="p-2 whitespace-nowrap w-20"><div class="font-semibold text-center">File</div></th>
								<th class="p-2 whitespace-nowrap w-48"><div class="font-semibold text-center">Aksi</div></th>
							</tr>
						</thead>
						<tbody class="text-sm">
							<template x-if="loading">
								<tr>
									<td colspan="10" class="p-4 text-center text-slate-500">
										<svg class="animate-spin h-8 w-8 mx-auto text-indigo-500" fill="none" viewBox="0 0 24 24">
											<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
											<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
										</svg>
									</td>
								</tr>
							</template>

							<template x-if="!loading && tasks.length === 0">
								<tr>
									<td colspan="10" class="p-4 text-center text-slate-500">Tidak ada data</td>
								</tr>
							</template>

							<template x-for="(task, idx) in tasks" :key="task.id">
								<tr class="border-b border-slate-200 dark:border-slate-700">
									<td class="p-2 whitespace-nowrap">
										<div class="font-medium text-slate-800 dark:text-slate-100" x-text="task.letter_number || 'DRAFT-' + task.id"></div>
										<div class="text-xs text-slate-500" x-text="formatDate(task.letter_date)"></div>
									</td>
									<td class="p-2">
										<div class="text-slate-800 dark:text-slate-100" x-text="task.subject"></div>
									</td>
									<td class="p-2 whitespace-nowrap">
										<div class="text-slate-800 dark:text-slate-100" x-text="task.agenda?.name || '-'"></div>
									</td>
									<td class="p-2 whitespace-nowrap text-xs">
										<div x-text="formatDate(task.created_at)"></div>
									</td>
									<td class="p-2 text-center">
										<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium" :class="getPriorityClass(task.priority)" x-text="getPriorityLabel(task.priority)"></span>
									</td>
									<td class="p-2 text-center">
										<span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium" :class="getStatusClass(task.overall_status)" x-text="getStatusLabel(task.overall_status)"></span>
									</td>
									<td class="p-2 text-center">
										<div class="flex flex-col items-center gap-1">
											<div class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2">
												<div class="bg-green-500 h-2 rounded-full" :style="`width: ${task.completion_rate || 0}%`"></div>
											</div>
											<span class="text-xs font-medium" x-text="(task.completion_rate || 0) + '%'"></span>
											<span class="text-xs text-slate-500" x-text="task.completed_recipients + '/' + task.total_recipients"></span>
										</div>
									</td>
									<td class="p-2 text-center">
										<button @click="open('showParticipants', task)" class="text-indigo-500 hover:text-indigo-600 font-medium">
											<span x-text="task.total_recipients || 0"></span>
										</button>
									</td>
									<td class="p-2 text-center">
										<button @click="open('showAttachments', task)" class="text-indigo-500 hover:text-indigo-600 font-medium" x-show="task.attachments?.length">
											<span x-text="task.attachments?.length || 0"></span>
										</button>
										<span x-show="!task.attachments?.length" class="text-slate-400">0</span>
									</td>
									<td class="p-2 text-center">
										<div class="flex items-center justify-center gap-1">
											<button @click="open('showView', task)" class="btn-sm bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300" title="Lihat Detail">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
												</svg>
											</button>
											<button @click="open('showHistory', task)" class="btn-sm bg-blue-100 dark:bg-blue-900 hover:bg-blue-200 dark:hover:bg-blue-800 text-blue-600 dark:text-blue-300" title="History">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
												</svg>
											</button>
											<template x-if="task.overall_status === 'draft' || task.overall_status === 'need_signature'">
												<button @click="open('showSign', task)" class="btn-sm bg-amber-100 dark:bg-amber-900 hover:bg-amber-200 dark:hover:bg-amber-800 text-amber-600 dark:text-amber-300" title="Tanda Tangan">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
													</svg>
												</button>
											</template>
											<template x-if="task.overall_status === 'signed'">
												<button @click="open('showPublish', task)" class="btn-sm bg-green-100 dark:bg-green-900 hover:bg-green-200 dark:hover:bg-green-800 text-green-600 dark:text-green-300" title="Publish">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
													</svg>
												</button>
											</template>
											<template x-if="task.overall_status === 'draft'">
												<button @click="open('showEdit', task)" class="btn-sm bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 text-indigo-600 dark:text-indigo-300" title="Edit">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
													</svg>
												</button>
											</template>
										</div>
									</td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Pagination -->
		<div class="mt-4 flex justify-between items-center" x-show="pagination.last_page > 1">
			<div class="text-sm text-slate-500">
				Menampilkan <span x-text="tasks.length"></span> dari <span x-text="pagination.total"></span> data
			</div>
			<div class="flex gap-1">
				<button @click="changePage(pagination.current_page - 1)" :disabled="pagination.current_page === 1" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 disabled:opacity-50">
					Sebelumnya
				</button>
				<template x-for="page in paginationPages" :key="page">
					<button @click="changePage(page)" :class="page === pagination.current_page ? 'bg-indigo-500 text-white' : 'border-slate-200 dark:border-slate-700'" class="btn">
						<span x-text="page"></span>
					</button>
				</template>
				<button @click="changePage(pagination.current_page + 1)" :disabled="pagination.current_page === pagination.last_page" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 disabled:opacity-50">
					Selanjutnya
				</button>
			</div>
		</div>

		<!-- Modals -->
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.view-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.preview-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.sign-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.publish-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.participants-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.history-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.edit-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.create-modal')
		@include('pages.rektorat.tindaklanjut-surat-tugas.detail.attachment-modal')
	</div>

	<script>
	function tindakLanjutSuratTugas() {
		return {
			tasks: [],
			loading: false,
			availableDepartments: [],
			availableUsers: [],
			filters: {
				q: '',
				date: '',
				status: '',
				priority: '',
				period_from: '',
				period_to: ''
			},
			pagination: {
				current_page: 1,
				last_page: 1,
				per_page: 10,
				total: 0
			},
			stats: {
				total: 0,
				completed: 0,
				in_progress: 0,
				pending: 0
			},
			selected: null,
			showCreate: false,
			showEdit: false,
			showView: false,
			showParticipants: false,
			showAttachments: false,
			showSign: false,
			showPublish: false,
			showHistory: false,
			showPreview: false,

			init() {
				this.fetchTasks();
				this.fetchDepartments();
				this.fetchUsers();
			},

			async fetchDepartments() {
				try {
					const response = await fetch('/rektor/api/departments');
					const data = await response.json();
					if (data.success) {
						this.availableDepartments = data.data ?? [];
					}
				} catch (error) {
					console.error('Error fetching departments:', error);
				}
			},

			async fetchUsers() {
				try {
					const response = await fetch('/rektor/api/users');
					const data = await response.json();
					if (data.success) {
						this.availableUsers = data.data ?? [];
					}
				} catch (error) {
					console.error('Error fetching users:', error);
				}
			},

			async fetchTasks(page = 1) {
				this.loading = true;
				try {
					const params = new URLSearchParams({
						page,
						...Object.fromEntries(Object.entries(this.filters).filter(([_, v]) => v))
					});

					const response = await fetch(`/rektor/api/tindak-lanjut?${params}`);
					const data = await response.json();

					this.tasks = data.data;
					this.pagination = data.meta;
					this.calculateStats();
				} catch (error) {
					console.error('Error fetching tasks:', error);
				} finally {
					this.loading = false;
				}
			},

			calculateStats() {
				this.stats.total = this.tasks.length;
				this.stats.completed = this.tasks.filter(t => t.completion_rate === 100).length;
				this.stats.in_progress = this.tasks.filter(t => t.completion_rate > 0 && t.completion_rate < 100).length;
				this.stats.pending = this.tasks.filter(t => t.completion_rate === 0).length;
			},

			applyFilters() {
				this.fetchTasks(1);
			},

			resetFilters() {
				this.filters = {
					q: '',
					date: '',
					status: '',
					priority: '',
					period_from: '',
					period_to: ''
				};
				this.fetchTasks(1);
			},

			changePage(page) {
				if (page >= 1 && page <= this.pagination.last_page) {
					this.fetchTasks(page);
				}
			},

			get paginationPages() {
				const pages = [];
				const start = Math.max(1, this.pagination.current_page - 2);
				const end = Math.min(this.pagination.last_page, this.pagination.current_page + 2);
				for (let i = start; i <= end; i++) {
					pages.push(i);
				}
				return pages;
			},

			open(modal, task = null) {
				this.selected = task;
				this[modal] = true;
			},

			closeAll() {
				this.showCreate = false;
				this.showEdit = false;
				this.showView = false;
				this.showParticipants = false;
				this.showAttachments = false;
				this.showSign = false;
				this.showPublish = false;
				this.showHistory = false;
			},

			formatDate(date) {
				if (!date) return '-';
				return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
			},

			getPriorityClass(priority) {
				const classes = {
					low: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
					normal: 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
					high: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
					urgent: 'bg-rose-500/10 text-rose-600 dark:text-rose-400'
				};
				return classes[priority] || classes.normal;
			},

			getPriorityLabel(priority) {
				const labels = {
					low: 'Rendah',
					normal: 'Normal',
					high: 'Tinggi',
					urgent: 'Mendesak'
				};
				return labels[priority] || priority;
			},

			getStatusClass(status) {
				const classes = {
					draft: 'bg-slate-500/10 text-slate-600 dark:text-slate-300',
					need_signature: 'bg-rose-500/10 text-rose-600 dark:text-rose-400',
					signed: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
					published: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
					archived: 'bg-slate-500/10 text-slate-600 dark:text-slate-400'
				};
				return classes[status] || classes.draft;
			},

			getStatusLabel(status) {
				const labels = {
					draft: 'Draft',
					need_signature: 'Perlu TTD',
					signed: 'Ditandatangani',
					published: 'Dipublikasi',
					archived: 'Diarsipkan'
				};
				return labels[status] || status;
			}
		}
	}
	</script>
</x-app-layout>
