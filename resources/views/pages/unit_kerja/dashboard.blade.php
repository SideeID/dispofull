<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" x-data="unitKerjaDashboard()">

		<!-- Header -->
		<div class="mb-8">
			<div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
				<div>
					<h1 class="text-2xl md:text-3xl text-gray-800 dark:text-gray-100 font-bold flex items-center gap-2">
						<i data-feather="layers" class="w-6 h-6 text-amber-500"></i>
						Dashboard Unit Kerja
					</h1>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ringkasan aktivitas surat & tugas unit kerja Anda</p>
				</div>
				<div class="flex items-center gap-2">
					<button @click="refreshDashboard" class="btn bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center gap-2 text-sm">
						<i data-feather="refresh-cw" class="w-4 h-4" :class="loading && 'animate-spin'"></i> Refresh
					</button>
					<a href="{{ route('unit_kerja.buat.surat') }}" class="btn bg-amber-600 hover:bg-amber-500 text-white flex items-center gap-2 text-sm">
						<i data-feather="file-plus" class="w-4 h-4"></i> Buat Surat
					</a>
				</div>
			</div>
		</div>

		<!-- Statistik Cards -->
		<div class="grid grid-cols-12 gap-6 mb-10">
			<!-- Surat Masuk Hari Ini -->
			<div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
				<div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4 h-full">
					<div class="flex items-start justify-between gap-4">
						<div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Surat Masuk (Hari Ini)</div>
						<div class="rounded-lg p-2 text-white shadow ring-1 bg-amber-500 ring-amber-400/40">
							<i data-feather="inbox" class="w-4 h-4"></i>
						</div>
					</div>
					<div class="flex items-end justify-between">
						<div class="text-3xl font-semibold text-gray-800 dark:text-gray-100" x-text="stats.incoming_today || 0"></div>
						<div class="text-[11px] font-medium" :class="getDeltaClass('incoming')">
							<span x-text="getDeltaText('incoming')"></span>
						</div>
					</div>
				</div>
			</div>

			<!-- Draft Surat Keluar -->
			<div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
				<div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4 h-full">
					<div class="flex items-start justify-between gap-4">
						<div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Draft Surat Keluar</div>
						<div class="rounded-lg p-2 text-white shadow ring-1 bg-blue-500 ring-blue-400/40">
							<i data-feather="edit" class="w-4 h-4"></i>
						</div>
					</div>
					<div class="flex items-end justify-between">
						<div class="text-3xl font-semibold text-gray-800 dark:text-gray-100" x-text="stats.draft_outgoing || 0"></div>
						<div class="text-[11px] font-medium text-slate-500 dark:text-slate-400">
							<span x-text="stats.draft_outgoing + ' draft'"></span>
						</div>
					</div>
				</div>
			</div>

			<!-- Menunggu Tanda Tangan -->
			<div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
				<div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4 h-full">
					<div class="flex items-start justify-between gap-4">
						<div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Menunggu Tanda Tangan</div>
						<div class="rounded-lg p-2 text-white shadow ring-1 bg-emerald-500 ring-emerald-400/40">
							<i data-feather="pen-tool" class="w-4 h-4"></i>
						</div>
					</div>
					<div class="flex items-end justify-between">
						<div class="text-3xl font-semibold text-gray-800 dark:text-gray-100" x-text="stats.awaiting_signature || 0"></div>
						<div class="text-[11px] font-medium" :class="stats.high_priority_pending > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-500 dark:text-slate-400'">
							<span x-text="stats.high_priority_pending > 0 ? stats.high_priority_pending + ' prioritas' : 'Normal'"></span>
						</div>
					</div>
				</div>
			</div>

			<!-- Surat Tugas Diarsipkan -->
			<div class="col-span-12 sm:col-span-6 md:col-span-4 xl:col-span-3">
				<div class="relative overflow-hidden rounded-xl bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 p-5 flex flex-col gap-4 h-full">
					<div class="flex items-start justify-between gap-4">
						<div class="text-[11px] font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">ST Diarsipkan (Bulan Ini)</div>
						<div class="rounded-lg p-2 text-white shadow ring-1 bg-slate-600 ring-slate-500/40">
							<i data-feather="archive" class="w-4 h-4"></i>
						</div>
					</div>
					<div class="flex items-end justify-between">
						<div class="text-3xl font-semibold text-gray-800 dark:text-gray-100" x-text="stats.archived_this_month || 0"></div>
						<div class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400">
							<span x-text="'+' + (stats.archived_this_week || 0) + ' minggu ini'"></span>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="grid grid-cols-12 gap-8">
			<!-- Kolom utama -->
			<div class="col-span-12 lg:col-span-8 space-y-8">
				<!-- Chart Surat Masuk/Keluar Bulanan -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden p-5">
					<div class="flex items-center justify-between mb-4">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="trending-up" class="w-4 h-4 text-indigo-500"></i> Trend Surat (6 Bulan Terakhir)
						</h2>
					</div>
					<div class="relative h-64">
						<canvas id="monthlyChart"></canvas>
					</div>
				</div>

				<!-- Surat Masuk Terbaru -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="inbox" class="w-4 h-4 text-amber-500"></i> Surat Masuk Terbaru
						</h2>
						<a href="{{ route('unit_kerja.inbox.disposisi') }}" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Lihat Semua</a>
					</div>
					<div class="overflow-x-auto">
						<table class="min-w-full text-sm">
							<thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-600 dark:text-gray-300">
								<tr class="text-xs uppercase tracking-wide">
									<th class="px-5 py-3 text-left font-semibold">Nomor</th>
									<th class="px-5 py-3 text-left font-semibold">Perihal</th>
									<th class="px-5 py-3 text-left font-semibold">Dari</th>
									<th class="px-5 py-3 text-left font-semibold">Tanggal</th>
									<th class="px-5 py-3 text-left font-semibold">Prioritas</th>
									<th class="px-5 py-3 text-left font-semibold">Status</th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
								<template x-if="recentIncoming.length === 0">
									<tr>
										<td colspan="6" class="px-5 py-8 text-center text-gray-500">Tidak ada surat masuk</td>
									</tr>
								</template>
								<template x-for="letter in recentIncoming" :key="letter.id">
									<tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40">
										<td class="px-5 py-3 font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="letter.number"></td>
										<td class="px-5 py-3 text-gray-700 dark:text-gray-200">
											<div class="font-medium line-clamp-1" x-text="letter.subject"></div>
										</td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300 text-xs" x-text="letter.from"></td>
										<td class="px-5 py-3 text-gray-600 dark:text-gray-300 text-xs" x-text="formatDate(letter.date)"></td>
										<td class="px-5 py-3">
											<span class="px-2 py-0.5 rounded-full text-[11px] font-medium capitalize" :class="getPriorityClass(letter.priority)" x-text="letter.priority"></span>
										</td>
										<td class="px-5 py-3">
											<span class="px-2 py-0.5 rounded-full text-[11px] font-medium capitalize" :class="getStatusClass(letter.status)" x-text="letter.status"></span>
										</td>
									</tr>
								</template>
							</tbody>
						</table>
					</div>
				</div>

				<!-- Draft Surat Keluar -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="file-text" class="w-4 h-4 text-blue-500"></i> Draft Surat Keluar
						</h2>
						<a href="{{ route('unit_kerja.buat.surat') }}" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Kelola Draft</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						<template x-if="draftOutgoing.length === 0">
							<li class="px-5 py-8 text-center text-gray-500">Tidak ada draft</li>
						</template>
						<template x-for="draft in draftOutgoing" :key="draft.id">
							<li class="px-5 py-3 hover:bg-gray-50/60 dark:hover:bg-gray-700/40 flex items-start gap-4">
								<div class="w-16">
									<div class="text-[11px] font-mono text-gray-500 dark:text-gray-400" x-text="draft.temp"></div>
									<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="formatTime(draft.created_at)"></div>
								</div>
								<div class="flex-1">
									<div class="flex items-center gap-2">
										<span class="text-gray-700 dark:text-gray-200 font-medium line-clamp-1" x-text="draft.subject"></span>
										<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-blue-500/10 text-blue-600 dark:text-blue-400" x-text="draft.type"></span>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize" :class="getPriorityClass(draft.priority)" x-text="draft.priority"></span>
									</div>
								</div>
								<div class="flex items-center gap-1">
									<a :href="`/unit-kerja/buat-surat?edit=${draft.id}`" class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-500 dark:text-gray-400" title="Edit">
										<i data-feather="edit-2" class="w-4 h-4"></i>
									</a>
								</div>
							</li>
						</template>
					</ul>
				</div>
			</div>

			<!-- Sidebar kanan -->
			<div class="col-span-12 lg:col-span-4 space-y-8">
				<!-- Notifikasi Disposisi Pending -->
				<div class="rounded-lg bg-gradient-to-br from-rose-50 to-amber-50 dark:from-rose-900/20 dark:to-amber-900/20 ring-1 ring-rose-200 dark:ring-rose-800 overflow-hidden">
					<div class="flex items-center justify-between px-5 py-4 border-b border-rose-100 dark:border-rose-800/60 bg-white/50 dark:bg-gray-800/50">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="bell" class="w-4 h-4 text-rose-500 animate-pulse"></i> 
							Disposisi Pending
							<span class="px-2 py-0.5 rounded-full bg-rose-500 text-white text-[10px] font-bold" x-show="pendingNotifications.length > 0" x-text="pendingNotifications.length"></span>
						</h2>
						<a href="{{ route('unit_kerja.inbox.disposisi') }}" class="text-[11px] font-medium text-rose-600 dark:text-rose-400 hover:underline">Lihat Semua</a>
					</div>
					<ul class="divide-y divide-rose-100 dark:divide-rose-800/60 text-sm max-h-96 overflow-y-auto">
						<template x-if="pendingNotifications.length === 0">
							<li class="px-5 py-6 text-center text-[11px] text-gray-500">
								<i data-feather="check-circle" class="w-8 h-8 mx-auto mb-2 text-green-500"></i>
								<div>Tidak ada disposisi pending</div>
							</li>
						</template>
						<template x-for="notif in pendingNotifications" :key="notif.id">
							<li class="px-5 py-3 hover:bg-white/70 dark:hover:bg-gray-800/70 transition cursor-pointer" @click="window.location.href = '/unit-kerja/inbox-disposisi'">
								<div class="flex items-start gap-3">
									<div class="flex-shrink-0 mt-1">
										<div class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></div>
									</div>
									<div class="flex-1 min-w-0">
										<div class="flex items-center gap-2 mb-1">
											<span class="font-mono text-[10px] text-gray-500 dark:text-gray-400" x-text="notif.letter_number"></span>
											<span class="px-1.5 py-0.5 rounded-full text-[9px] font-medium capitalize" :class="getPriorityClass(notif.priority)" x-text="notif.priority"></span>
										</div>
										<div class="text-gray-700 dark:text-gray-200 text-xs font-medium line-clamp-2 mb-1" x-text="notif.subject"></div>
										<div class="flex items-center gap-2 text-[10px] text-gray-500 dark:text-gray-400">
											<span>Dari: <span class="font-medium" x-text="notif.from"></span></span>
											<span>•</span>
											<span x-text="notif.created_at"></span>
										</div>
										<div x-show="notif.notes" class="mt-2 text-[10px] text-gray-600 dark:text-gray-300 bg-white/70 dark:bg-gray-700/50 rounded p-2">
											<span x-text="notif.notes"></span>
										</div>
									</div>
								</div>
							</li>
						</template>
					</ul>
				</div>

				<!-- Chart Status Disposisi -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden p-5">
					<div class="flex items-center justify-between mb-4">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="pie-chart" class="w-4 h-4 text-blue-500"></i> Status Disposisi
						</h2>
					</div>
					<div class="relative h-48 flex items-center justify-center">
						<canvas id="dispositionChart"></canvas>
					</div>
					<div class="mt-4 grid grid-cols-3 gap-2 text-xs">
						<div class="text-center">
							<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="stats.dispositions_unread || 0"></div>
							<div class="text-gray-500 dark:text-gray-400">Pending</div>
						</div>
						<div class="text-center border-l border-gray-200 dark:border-gray-700">
							<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="stats.dispositions_in_progress || 0"></div>
							<div class="text-gray-500 dark:text-gray-400">Proses</div>
						</div>
						<div class="text-center border-l border-gray-200 dark:border-gray-700">
							<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="chartData.completed || 0"></div>
							<div class="text-gray-500 dark:text-gray-400">Selesai</div>
						</div>
					</div>
				</div>

				<!-- Arsip Snapshot -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="archive" class="w-4 h-4 text-slate-500"></i> Arsip ST Terbaru
						</h2>
						<a href="{{ route('unit_kerja.arsip.surat.tugas') }}" class="text-[11px] font-medium text-amber-600 dark:text-amber-400 hover:underline">Lihat</a>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						<template x-if="archivedAssignments.length === 0">
							<li class="px-5 py-6 text-center text-[11px] text-gray-400 dark:text-gray-500">Tidak ada arsip</li>
						</template>
						<template x-for="archive in archivedAssignments" :key="archive.id">
							<li class="px-5 py-3 hover:bg-gray-50/60 dark:hover:bg-gray-700/40">
								<div class="font-mono text-[11px] text-gray-500 dark:text-gray-400" x-text="archive.number"></div>
								<div class="text-gray-700 dark:text-gray-200 text-xs font-medium line-clamp-1" x-text="archive.subject"></div>
								<div class="text-[10px] text-gray-400 dark:text-gray-500">
									<span>Arsip: <span x-text="archive.archived_at"></span></span> · 
									<span>Durasi <span x-text="archive.duration"></span>h</span>
								</div>
							</li>
						</template>
					</ul>
				</div>

				<!-- Antrian Tanda Tangan -->
				<div class="rounded-lg bg-white dark:bg-gray-800 ring-1 ring-gray-200 dark:ring-gray-700">
					<div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-gray-700/60">
						<h2 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2 text-sm">
							<i data-feather="pen-tool" class="w-4 h-4 text-emerald-500"></i> Antrian TTD
						</h2>
					</div>
					<ul class="divide-y divide-gray-100 dark:divide-gray-700/60 text-sm">
						<template x-if="signatureQueue.length === 0">
							<li class="px-5 py-6 text-center text-[11px] text-gray-400 dark:text-gray-500">Tidak ada antrean</li>
						</template>
						<template x-for="queue in signatureQueue" :key="queue.id">
							<li class="px-5 py-3 flex items-start gap-4 hover:bg-gray-50/60 dark:hover:bg-gray-700/40">
								<div class="w-16">
									<div class="text-[11px] font-mono text-gray-500 dark:text-gray-400" x-text="queue.temp"></div>
									<div class="text-[10px] text-gray-400 dark:text-gray-500" x-text="formatTime(queue.requested_at)"></div>
								</div>
								<div class="flex-1">
									<div class="flex items-center gap-2">
										<span class="text-gray-700 dark:text-gray-200 text-xs font-medium line-clamp-1" x-text="queue.subject"></span>
										<span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400" x-text="queue.type"></span>
										<span class="px-2 py-0.5 rounded-full text-[10px] font-medium capitalize" :class="getPriorityClass(queue.priority)" x-text="queue.priority"></span>
									</div>
								</div>
							</li>
						</template>
					</ul>
				</div>
			</div>
		</div>

		<div class="mt-10 text-center text-[11px] text-gray-400 dark:text-gray-600">Sistem Pengelolaan Surat · Universitas Bakrie · Dashboard Unit Kerja</div>
	</div>

	<!-- Chart.js CDN -->
	<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

	<script>
	function unitKerjaDashboard() {
		return {
			loading: false,
			stats: {},
			recentIncoming: [],
			draftOutgoing: [],
			archivedAssignments: [],
			signatureQueue: [],
			pendingNotifications: [],
			chartData: {
				completed: 0
			},
			monthlyChart: null,
			dispositionChart: null,

			async init() {
				await this.fetchAll();
				this.initCharts();
			},

			async fetchAll() {
				this.loading = true;
				try {
					await Promise.all([
						this.fetchStats(),
						this.fetchRecentIncoming(),
						this.fetchDraftOutgoing(),
						this.fetchArchivedAssignments(),
						this.fetchSignatureQueue(),
						this.fetchPendingNotifications(),
						this.fetchChartData()
					]);
				} finally {
					this.loading = false;
				}
			},

			async fetchStats() {
				const response = await fetch('/unit-kerja/api/dashboard/stats');
				const data = await response.json();
				this.stats = data.data;
			},

			async fetchRecentIncoming() {
				const response = await fetch('/unit-kerja/api/dashboard/recent-incoming');
				const data = await response.json();
				this.recentIncoming = data.data;
			},

			async fetchDraftOutgoing() {
				const response = await fetch('/unit-kerja/api/dashboard/draft-outgoing');
				const data = await response.json();
				this.draftOutgoing = data.data;
			},

			async fetchArchivedAssignments() {
				const response = await fetch('/unit-kerja/api/dashboard/archived-assignments');
				const data = await response.json();
				this.archivedAssignments = data.data;
			},

			async fetchSignatureQueue() {
				const response = await fetch('/unit-kerja/api/dashboard/signature-queue');
				const data = await response.json();
				this.signatureQueue = data.data;
			},

			async fetchPendingNotifications() {
				const response = await fetch('/unit-kerja/api/dashboard/pending-notifications');
				const data = await response.json();
				this.pendingNotifications = data.data;
			},

			async fetchChartData() {
				// Monthly chart
				const monthlyResponse = await fetch('/unit-kerja/api/dashboard/chart-monthly');
				const monthlyData = await monthlyResponse.json();
				this.updateMonthlyChart(monthlyData.data);

				// Disposition status chart
				const dispositionResponse = await fetch('/unit-kerja/api/dashboard/chart-disposition-status');
				const dispositionData = await dispositionResponse.json();
				this.updateDispositionChart(dispositionData.data);
				
				// Store completed count
				this.chartData.completed = dispositionData.data.datasets[0].data[2];
			},

			initCharts() {
				// Monthly chart
				const monthlyCtx = document.getElementById('monthlyChart');
				if (monthlyCtx) {
					this.monthlyChart = new Chart(monthlyCtx, {
						type: 'line',
						data: { labels: [], datasets: [] },
						options: {
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								legend: {
									position: 'bottom'
								}
							},
							scales: {
								y: {
									beginAtZero: true,
									ticks: {
										stepSize: 1
									}
								}
							}
						}
					});
				}

				// Disposition chart
				const dispositionCtx = document.getElementById('dispositionChart');
				if (dispositionCtx) {
					this.dispositionChart = new Chart(dispositionCtx, {
						type: 'doughnut',
						data: { labels: [], datasets: [] },
						options: {
							responsive: true,
							maintainAspectRatio: false,
							plugins: {
								legend: {
									position: 'bottom',
									labels: {
										boxWidth: 12,
										font: {
											size: 10
										}
									}
								}
							}
						}
					});
				}
			},

			updateMonthlyChart(data) {
				if (this.monthlyChart) {
					this.monthlyChart.data = data;
					this.monthlyChart.update();
				}
			},

			updateDispositionChart(data) {
				if (this.dispositionChart) {
					this.dispositionChart.data = data;
					this.dispositionChart.update();
				}
			},

			async refreshDashboard() {
				await this.fetchAll();
			},

			getDeltaClass(type) {
				if (type === 'incoming') {
					const delta = this.stats.incoming_today - this.stats.incoming_yesterday;
					if (delta > 0) return 'text-emerald-600 dark:text-emerald-400';
					if (delta < 0) return 'text-rose-600 dark:text-rose-400';
				}
				return 'text-slate-500 dark:text-slate-400';
			},

			getDeltaText(type) {
				if (type === 'incoming') {
					const delta = this.stats.incoming_today - this.stats.incoming_yesterday;
					if (delta > 0) return `+${delta} vs kemarin`;
					if (delta < 0) return `${delta} vs kemarin`;
					return 'sama dengan kemarin';
				}
				return '';
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

			getStatusClass(status) {
				const classes = {
					pending: 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
					in_progress: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
					completed: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
					processed: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
					review: 'bg-indigo-500/10 text-indigo-600 dark:text-indigo-400'
				};
				return classes[status] || classes.pending;
			},

			formatDate(date) {
				if (!date) return '-';
				return new Date(date).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
			},

			formatTime(datetime) {
				if (!datetime) return '-';
				return datetime.split(' ')[1]?.substring(0, 5) || '-';
			}
		}
	}
	</script>
</x-app-layout>
