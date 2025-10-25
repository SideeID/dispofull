<x-app-layout>
	<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-7xl mx-auto"
		 x-data="agendaManagement()"
		 @keydown.escape.window="closeAll()"
	>
		<!-- Header -->
		<div class="mb-6 flex justify-between items-center">
			<div>
				<h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Pembuatan Agenda Surat</h1>
				<p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola agenda surat harian, mingguan, dan bulanan</p>
			</div>
			<div class="flex gap-2">
				<button @click="openAutoGenerateModal()" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
					</svg>
					Auto Generate
				</button>
				<button @click="openCreateModal()" class="btn bg-emerald-500 hover:bg-emerald-600 text-white">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
					</svg>
					Buat Agenda
				</button>
			</div>
		</div>

		<!-- Filters -->
		<div class="bg-white dark:bg-slate-800 shadow rounded-sm mb-5">
			<div class="p-4">
				<form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-5 gap-4">
					<div>
						<label class="block text-sm font-medium mb-1">Cari</label>
						<input type="text" x-model="filters.search" placeholder="Cari judul..." class="form-input w-full">
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Tipe Agenda</label>
						<select x-model="filters.type" class="form-select w-full">
							<option value="">Semua Tipe</option>
							<option value="daily">Harian</option>
							<option value="weekly">Mingguan</option>
							<option value="monthly">Bulanan</option>
						</select>
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Status</label>
						<select x-model="filters.status" class="form-select w-full">
							<option value="">Semua Status</option>
							<option value="draft">Draft</option>
							<option value="published">Published</option>
							<option value="archived">Archived</option>
						</select>
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Dari Tanggal</label>
						<input type="date" x-model="filters.date_from" class="form-input w-full">
					</div>

					<div>
						<label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
						<input type="date" x-model="filters.date_to" class="form-input w-full">
					</div>
				</form>

				<div class="flex justify-end gap-2 mt-4">
					<button type="button" @click="resetFilters()" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600 dark:text-slate-300">
						Reset
					</button>
					<button type="button" @click="applyFilters()" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
						Terapkan Filter
					</button>
				</div>
			</div>
		</div>

		<!-- Agenda Table -->
		<div class="bg-white dark:bg-slate-800 shadow-lg rounded-sm border border-slate-200 dark:border-slate-700">
			<div class="p-3">
				<!-- Loading State -->
				<div x-show="loading" class="text-center py-8">
					<svg class="animate-spin h-8 w-8 text-indigo-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
					<p class="text-slate-500 dark:text-slate-400 mt-2">Memuat data...</p>
				</div>

				<!-- Empty State -->
				<div x-show="!loading && agendas.length === 0" class="text-center py-8">
					<svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
					</svg>
					<p class="text-slate-500 dark:text-slate-400">Belum ada agenda</p>
				</div>

				<!-- Table -->
				<div x-show="!loading && agendas.length > 0" class="overflow-x-auto">
					<table class="table-auto w-full dark:text-slate-300">
						<thead class="text-xs uppercase text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/20 border-t border-b border-slate-200 dark:border-slate-700">
							<tr>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-left">Judul</div>
								</th>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-left">Tipe</div>
								</th>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-left">Periode</div>
								</th>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-left">Departemen</div>
								</th>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-left">Status</div>
								</th>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-center">Jumlah Surat</div>
								</th>
								<th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
									<div class="font-semibold text-center">Aksi</div>
								</th>
							</tr>
						</thead>
						<tbody class="text-sm divide-y divide-slate-200 dark:divide-slate-700">
							<template x-for="agenda in agendas" :key="agenda.id">
								<tr>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
										<div class="font-medium text-slate-800 dark:text-slate-100" x-text="agenda.title"></div>
										<div class="text-xs text-slate-500" x-text="agenda.description"></div>
									</td>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
										<span x-text="getTypeLabel(agenda.type)" class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 text-xs"
											:class="{
												'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300': agenda.type === 'daily',
												'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300': agenda.type === 'weekly',
												'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-300': agenda.type === 'monthly'
											}"></span>
									</td>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
										<div class="text-slate-800 dark:text-slate-100" x-text="formatDateRange(agenda.start_date, agenda.end_date)"></div>
										<div class="text-xs text-slate-500" x-text="'Tanggal: ' + formatDate(agenda.agenda_date)"></div>
									</td>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
										<span x-text="agenda.department?.name || 'Semua'"></span>
									</td>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
										<span x-text="getStatusLabel(agenda.status)" class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 text-xs"
											:class="{
												'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-300': agenda.status === 'draft',
												'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300': agenda.status === 'published',
												'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300': agenda.status === 'archived'
											}"></span>
									</td>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">
										<span class="font-semibold" x-text="agenda.letters_count || 0"></span>
									</td>
									<td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap text-center">
										<div class="flex justify-center gap-1">
											<button @click="viewAgenda(agenda.id)" class="btn-sm bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-indigo-500" title="Detail">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
												</svg>
											</button>
											<button x-show="agenda.status !== 'archived'" @click="openEditModal(agenda)" class="btn-sm bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600" title="Edit">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
												</svg>
											</button>
											<button x-show="agenda.status === 'published'" @click="previewPDF(agenda.id)" class="btn-sm bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-blue-500" title="Preview PDF">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
												</svg>
											</button>
											<button x-show="agenda.status === 'published'" @click="exportPDF(agenda.id)" class="btn-sm bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-emerald-500" title="Download PDF">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
												</svg>
											</button>
											<button x-show="agenda.status === 'draft'" @click="publishAgenda(agenda.id)" class="btn-sm bg-emerald-500 hover:bg-emerald-600 text-white" title="Publish">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
												</svg>
											</button>
											<button x-show="agenda.status === 'published'" @click="archiveAgenda(agenda.id)" class="btn-sm bg-amber-500 hover:bg-amber-600 text-white" title="Archive">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
												</svg>
											</button>
											<button @click="deleteAgenda(agenda.id)" class="btn-sm bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-rose-500" title="Hapus">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
												</svg>
											</button>
										</div>
									</td>
								</tr>
							</template>
						</tbody>
					</table>
				</div>

				<!-- Pagination -->
				<div x-show="!loading && agendas.length > 0" class="mt-4 flex justify-between items-center">
					<div class="text-sm text-slate-500 dark:text-slate-400">
						Menampilkan <span x-text="pagination.from"></span> - <span x-text="pagination.to"></span> dari <span x-text="pagination.total"></span> agenda
					</div>
					<div class="flex gap-1">
						<template x-for="page in paginationPages" :key="page">
							<button 
								@click="goToPage(page)" 
								x-text="page"
								class="px-3 py-1 text-sm rounded"
								:class="page === pagination.current_page ? 'bg-indigo-500 text-white' : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700'"
							></button>
						</template>
					</div>
				</div>
			</div>
		</div>

		<!-- Create/Edit Modal -->
		<div x-show="showCreateModal" x-cloak class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" @click="closeAll()">
			<div @click.stop class="absolute inset-0 flex items-center justify-center p-4">
				<div class="bg-white dark:bg-slate-800 rounded shadow-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
					<div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
						<h2 class="font-semibold text-slate-800 dark:text-slate-100" x-text="isEditing ? 'Edit Agenda' : 'Buat Agenda Baru'"></h2>
					</div>
					<form @submit.prevent="saveAgenda()" class="px-5 py-4">
						<div class="space-y-4">
							<!-- Tipe Agenda -->
							<div>
								<label class="block text-sm font-medium mb-1">Tipe Agenda <span class="text-rose-500">*</span></label>
								<select x-model="formData.type" required class="form-select w-full">
									<option value="">Pilih Tipe</option>
									<option value="daily">Harian</option>
									<option value="weekly">Mingguan</option>
									<option value="monthly">Bulanan</option>
								</select>
							</div>

							<!-- Judul -->
							<div>
								<label class="block text-sm font-medium mb-1">Judul <span class="text-rose-500">*</span></label>
								<input type="text" x-model="formData.title" required placeholder="Contoh: Agenda Surat Masuk Harian" class="form-input w-full">
							</div>

							<!-- Deskripsi -->
							<div>
								<label class="block text-sm font-medium mb-1">Deskripsi</label>
								<textarea x-model="formData.description" rows="3" placeholder="Deskripsi agenda..." class="form-input w-full"></textarea>
							</div>

							<!-- Department -->
							<div>
								<label class="block text-sm font-medium mb-1">Departemen</label>
								<select x-model="formData.department_id" class="form-select w-full">
									<option value="">Semua Departemen</option>
									<template x-for="dept in filterOptions.departments" :key="dept.id">
										<option :value="dept.id" x-text="dept.name"></option>
									</template>
								</select>
							</div>

							<!-- Filter Section -->
							<div class="border-t border-slate-200 dark:border-slate-700 pt-4">
								<h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-3">Filter Surat</h3>
								
								<div class="space-y-3">
									<!-- Letter Types -->
									<div>
										<label class="block text-sm font-medium mb-1">Jenis Surat</label>
										<div class="max-h-32 overflow-y-auto border border-slate-200 dark:border-slate-700 rounded p-2">
											<template x-for="type in filterOptions.letter_types" :key="type.id">
												<label class="flex items-center py-1">
													<input type="checkbox" :value="type.id" x-model="formData.filters.letter_types" class="form-checkbox">
													<span class="ml-2 text-sm" x-text="type.name"></span>
												</label>
											</template>
										</div>
									</div>

									<!-- Direction -->
									<div>
										<label class="block text-sm font-medium mb-1">Arah Surat</label>
										<div class="flex gap-4">
											<label class="flex items-center">
												<input type="checkbox" value="incoming" x-model="formData.filters.direction" class="form-checkbox">
												<span class="ml-2 text-sm">Surat Masuk</span>
											</label>
											<label class="flex items-center">
												<input type="checkbox" value="outgoing" x-model="formData.filters.direction" class="form-checkbox">
												<span class="ml-2 text-sm">Surat Keluar</span>
											</label>
										</div>
									</div>

									<!-- Status -->
									<div>
										<label class="block text-sm font-medium mb-1">Status Surat</label>
										<div class="flex flex-wrap gap-3">
											<label class="flex items-center">
												<input type="checkbox" value="draft" x-model="formData.filters.status" class="form-checkbox">
												<span class="ml-2 text-sm">Draft</span>
											</label>
											<label class="flex items-center">
												<input type="checkbox" value="pending" x-model="formData.filters.status" class="form-checkbox">
												<span class="ml-2 text-sm">Pending</span>
											</label>
											<label class="flex items-center">
												<input type="checkbox" value="signed" x-model="formData.filters.status" class="form-checkbox">
												<span class="ml-2 text-sm">Signed</span>
											</label>
											<label class="flex items-center">
												<input type="checkbox" value="archived" x-model="formData.filters.status" class="form-checkbox">
												<span class="ml-2 text-sm">Archived</span>
											</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="flex justify-end gap-2 mt-6">
							<button type="button" @click="closeAll()" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600 dark:text-slate-300">
								Batal
							</button>
							<button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white" :disabled="saving">
								<span x-show="!saving" x-text="isEditing ? 'Update' : 'Simpan'"></span>
								<span x-show="saving">Menyimpan...</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Auto Generate Modal -->
		<div x-show="showAutoGenerateModal" x-cloak class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" @click="closeAll()">
			<div @click.stop class="absolute inset-0 flex items-center justify-center p-4">
				<div class="bg-white dark:bg-slate-800 rounded shadow-lg max-w-md w-full">
					<div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
						<h2 class="font-semibold text-slate-800 dark:text-slate-100">Auto Generate Agenda</h2>
					</div>
					<form @submit.prevent="runAutoGenerate()" class="px-5 py-4">
						<div class="space-y-4">
							<div>
								<label class="block text-sm font-medium mb-1">Tipe Agenda <span class="text-rose-500">*</span></label>
								<select x-model="autoGenerateType" required class="form-select w-full">
									<option value="">Pilih Tipe</option>
									<option value="daily">Harian</option>
									<option value="weekly">Mingguan</option>
									<option value="monthly">Bulanan</option>
								</select>
							</div>
							<div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded p-3 text-sm">
								<p class="text-blue-800 dark:text-blue-300">
									<strong>Info:</strong> Sistem akan membuat agenda secara otomatis berdasarkan tanggal saat ini.
								</p>
								<ul class="mt-2 ml-4 list-disc text-blue-700 dark:text-blue-400">
									<li>Harian: Agenda untuk hari ini</li>
									<li>Mingguan: Agenda untuk minggu ini</li>
									<li>Bulanan: Agenda untuk bulan ini</li>
								</ul>
							</div>
						</div>

						<div class="flex justify-end gap-2 mt-6">
							<button type="button" @click="closeAll()" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600 dark:text-slate-300">
								Batal
							</button>
							<button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white" :disabled="saving">
								<span x-show="!saving">Generate</span>
								<span x-show="saving">Generating...</span>
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Detail Modal -->
		<div x-show="showDetailModal" x-cloak class="fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity" @click="closeAll()">
			<div @click.stop class="absolute inset-0 flex items-center justify-center p-4">
				<div class="bg-white dark:bg-slate-800 rounded shadow-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
					<div class="px-5 py-4 border-b border-slate-200 dark:border-slate-700">
						<h2 class="font-semibold text-slate-800 dark:text-slate-100">Detail Agenda</h2>
					</div>
					<div class="px-5 py-4" x-show="selectedAgenda">
						<div class="space-y-4">
							<!-- Agenda Info -->
							<div class="grid grid-cols-2 gap-4">
								<div>
									<label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Judul</label>
									<p class="text-slate-800 dark:text-slate-100" x-text="selectedAgenda?.title"></p>
								</div>
								<div>
									<label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Tipe</label>
									<span x-text="getTypeLabel(selectedAgenda?.type)" class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 text-xs"
										:class="{
											'bg-blue-100 text-blue-800': selectedAgenda?.type === 'daily',
											'bg-purple-100 text-purple-800': selectedAgenda?.type === 'weekly',
											'bg-pink-100 text-pink-800': selectedAgenda?.type === 'monthly'
										}"></span>
								</div>
								<div>
									<label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Periode</label>
									<p class="text-slate-800 dark:text-slate-100" x-text="formatDateRange(selectedAgenda?.start_date, selectedAgenda?.end_date)"></p>
								</div>
								<div>
									<label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Status</label>
									<span x-text="getStatusLabel(selectedAgenda?.status)" class="inline-flex font-medium rounded-full text-center px-2.5 py-0.5 text-xs"
										:class="{
											'bg-slate-100 text-slate-800': selectedAgenda?.status === 'draft',
											'bg-emerald-100 text-emerald-800': selectedAgenda?.status === 'published',
											'bg-amber-100 text-amber-800': selectedAgenda?.status === 'archived'
										}"></span>
								</div>
							</div>

							<div x-show="selectedAgenda?.description">
								<label class="block text-sm font-medium text-slate-500 dark:text-slate-400 mb-1">Deskripsi</label>
								<p class="text-slate-800 dark:text-slate-100" x-text="selectedAgenda?.description"></p>
							</div>

							<!-- Letters List -->
							<div class="border-t border-slate-200 dark:border-slate-700 pt-4">
								<h3 class="font-semibold text-slate-800 dark:text-slate-100 mb-3">
									Daftar Surat (<span x-text="selectedAgenda?.letters?.length || 0"></span>)
								</h3>
								<div class="max-h-64 overflow-y-auto">
									<template x-if="!selectedAgenda?.letters || selectedAgenda.letters.length === 0">
										<p class="text-slate-500 dark:text-slate-400 text-sm text-center py-4">Tidak ada surat</p>
									</template>
									<table x-show="selectedAgenda?.letters && selectedAgenda.letters.length > 0" class="w-full text-sm">
										<thead class="text-xs uppercase text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/20">
											<tr>
												<th class="px-2 py-2 text-left">No</th>
												<th class="px-2 py-2 text-left">Nomor Surat</th>
												<th class="px-2 py-2 text-left">Perihal</th>
												<th class="px-2 py-2 text-left">Jenis</th>
												<th class="px-2 py-2 text-left">Tanggal</th>
											</tr>
										</thead>
										<tbody class="divide-y divide-slate-200 dark:divide-slate-700">
											<template x-for="(letter, index) in selectedAgenda?.letters" :key="letter.id">
												<tr>
													<td class="px-2 py-2" x-text="index + 1"></td>
													<td class="px-2 py-2" x-text="letter.letter_number"></td>
													<td class="px-2 py-2" x-text="letter.subject"></td>
													<td class="px-2 py-2" x-text="letter.letter_type?.name"></td>
													<td class="px-2 py-2" x-text="formatDate(letter.letter_date)"></td>
												</tr>
											</template>
										</tbody>
									</table>
								</div>
							</div>
						</div>

						<div class="flex justify-end gap-2 mt-6">
							<button type="button" @click="closeAll()" class="btn border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 text-slate-600 dark:text-slate-300">
								Tutup
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
	function agendaManagement() {
		return {
			// State
			loading: false,
			saving: false,
			agendas: [],
			selectedAgenda: null,
			pagination: {
				current_page: 1,
				from: 0,
				to: 0,
				total: 0,
				last_page: 1
			},
			
			// Filters
			filters: {
				search: '',
				type: '',
				status: '',
				date_from: '',
				date_to: ''
			},
			
			// Modals
			showCreateModal: false,
			showDetailModal: false,
			showAutoGenerateModal: false,
			isEditing: false,
			
			// Form Data
			formData: {
				title: '',
				description: '',
				type: '',
				department_id: '',
				filters: {
					letter_types: [],
					direction: [],
					status: []
				}
			},
			
			// Auto Generate
			autoGenerateType: '',
			
			// Filter Options
			filterOptions: {
				letter_types: [],
				departments: []
			},
			
			init() {
				this.loadFilterOptions();
				this.loadAgendas();
			},
			
			async loadFilterOptions() {
				try {
					const response = await fetch('/agenda/filter-options');
					const data = await response.json();
					this.filterOptions = data.data;
				} catch (error) {
					console.error('Failed to load filter options:', error);
				}
			},
			
			async loadAgendas(page = 1) {
				this.loading = true;
				try {
					const params = new URLSearchParams({
						page: page,
						...this.filters
					});
					
					const response = await fetch(`/agenda/list?${params}`);
					const data = await response.json();
					
					this.agendas = data.data;
					this.pagination = data.meta;
				} catch (error) {
					console.error('Failed to load agendas:', error);
					this.$dispatch('notify', { message: 'Gagal memuat agenda', type: 'error' });
				} finally {
					this.loading = false;
				}
			},
			
			applyFilters() {
				this.loadAgendas(1);
			},
			
			resetFilters() {
				this.filters = {
					search: '',
					type: '',
					status: '',
					date_from: '',
					date_to: ''
				};
				this.loadAgendas(1);
			},
			
			goToPage(page) {
				if (page !== this.pagination.current_page) {
					this.loadAgendas(page);
				}
			},
			
			get paginationPages() {
				const pages = [];
				const current = this.pagination.current_page;
				const last = this.pagination.last_page;
				
				for (let i = Math.max(1, current - 2); i <= Math.min(last, current + 2); i++) {
					pages.push(i);
				}
				return pages;
			},
			
			openCreateModal() {
				this.isEditing = false;
				this.formData = {
					title: '',
					description: '',
					type: '',
					department_id: '',
					filters: {
						letter_types: [],
						direction: [],
						status: []
					}
				};
				this.showCreateModal = true;
			},
			
			openEditModal(agenda) {
				this.isEditing = true;
				this.formData = {
					id: agenda.id,
					title: agenda.title,
					description: agenda.description,
					type: agenda.type,
					department_id: agenda.department_id || '',
					filters: agenda.filters || {
						letter_types: [],
						direction: [],
						status: []
					}
				};
				this.showCreateModal = true;
			},
			
			async saveAgenda() {
				this.saving = true;
				try {
					const url = this.isEditing ? `/agenda/${this.formData.id}` : '/agenda';
					const method = this.isEditing ? 'PUT' : 'POST';
					
					const response = await fetch(url, {
						method: method,
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						},
						body: JSON.stringify(this.formData)
					});
					
					const data = await response.json();
					
					if (data.success) {
						this.$dispatch('notify', { message: data.message, type: 'success' });
						this.closeAll();
						this.loadAgendas();
					} else {
						this.$dispatch('notify', { message: data.message || 'Gagal menyimpan agenda', type: 'error' });
					}
				} catch (error) {
					console.error('Failed to save agenda:', error);
					this.$dispatch('notify', { message: 'Gagal menyimpan agenda', type: 'error' });
				} finally {
					this.saving = false;
				}
			},
			
			async viewAgenda(id) {
				try {
					const response = await fetch(`/agenda/${id}`);
					const data = await response.json();
					this.selectedAgenda = data.data;
					this.showDetailModal = true;
				} catch (error) {
					console.error('Failed to load agenda details:', error);
					this.$dispatch('notify', { message: 'Gagal memuat detail agenda', type: 'error' });
				}
			},
			
			async publishAgenda(id) {
				if (!confirm('Publish agenda ini? Agenda akan tersedia untuk diexport ke PDF.')) return;
				
				try {
					const response = await fetch(`/agenda/${id}/publish`, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						}
					});
					
					const data = await response.json();
					
					if (data.success) {
						this.$dispatch('notify', { message: data.message, type: 'success' });
						this.loadAgendas();
					} else {
						this.$dispatch('notify', { message: data.message || 'Gagal publish agenda', type: 'error' });
					}
				} catch (error) {
					console.error('Failed to publish agenda:', error);
					this.$dispatch('notify', { message: 'Gagal publish agenda', type: 'error' });
				}
			},
			
			async archiveAgenda(id) {
				if (!confirm('Archive agenda ini?')) return;
				
				try {
					const response = await fetch(`/agenda/${id}/archive`, {
						method: 'POST',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						}
					});
					
					const data = await response.json();
					
					if (data.success) {
						this.$dispatch('notify', { message: data.message, type: 'success' });
						this.loadAgendas();
					} else {
						this.$dispatch('notify', { message: data.message || 'Gagal archive agenda', type: 'error' });
					}
				} catch (error) {
					console.error('Failed to archive agenda:', error);
					this.$dispatch('notify', { message: 'Gagal archive agenda', type: 'error' });
				}
			},
			
			async deleteAgenda(id) {
				if (!confirm('Hapus agenda ini? Aksi tidak dapat dibatalkan.')) return;
				
				try {
					const response = await fetch(`/agenda/${id}`, {
						method: 'DELETE',
						headers: {
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						}
					});
					
					const data = await response.json();
					
					if (data.success) {
						this.$dispatch('notify', { message: data.message, type: 'success' });
						this.loadAgendas();
					} else {
						this.$dispatch('notify', { message: data.message || 'Gagal menghapus agenda', type: 'error' });
					}
				} catch (error) {
					console.error('Failed to delete agenda:', error);
					this.$dispatch('notify', { message: 'Gagal menghapus agenda', type: 'error' });
				}
			},
			
			previewPDF(id) {
				window.open(`/agenda/${id}/preview-pdf`, '_blank');
			},
			
			exportPDF(id) {
				window.location.href = `/agenda/${id}/export-pdf`;
			},
			
			openAutoGenerateModal() {
				this.autoGenerateType = '';
				this.showAutoGenerateModal = true;
			},
			
			async runAutoGenerate() {
				this.saving = true;
				try {
					const response = await fetch('/agenda/auto-generate', {
						method: 'POST',
						headers: {
							'Content-Type': 'application/json',
							'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
						},
						body: JSON.stringify({ type: this.autoGenerateType })
					});
					
					const data = await response.json();
					
					if (data.success) {
						this.$dispatch('notify', { message: data.message, type: 'success' });
						this.closeAll();
						this.loadAgendas();
					} else {
						this.$dispatch('notify', { message: data.message || 'Gagal generate agenda', type: 'error' });
					}
				} catch (error) {
					console.error('Failed to auto-generate agenda:', error);
					this.$dispatch('notify', { message: 'Gagal generate agenda', type: 'error' });
				} finally {
					this.saving = false;
				}
			},
			
			closeAll() {
				this.showCreateModal = false;
				this.showDetailModal = false;
				this.showAutoGenerateModal = false;
				this.selectedAgenda = null;
			},
			
			formatDate(date) {
				if (!date) return '-';
				return new Date(date).toLocaleDateString('id-ID', {
					day: '2-digit',
					month: 'short',
					year: 'numeric'
				});
			},
			
			formatDateRange(start, end) {
				if (!start || !end) return '-';
				return `${this.formatDate(start)} - ${this.formatDate(end)}`;
			},
			
			getTypeLabel(type) {
				const labels = {
					daily: 'Harian',
					weekly: 'Mingguan',
					monthly: 'Bulanan'
				};
				return labels[type] || type;
			},
			
			getStatusLabel(status) {
				const labels = {
					draft: 'Draft',
					published: 'Published',
					archived: 'Archived'
				};
				return labels[status] || status;
			}
		}
	}
	</script>
</x-app-layout>
