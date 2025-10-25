<template x-if="showFollowupList">
	<div class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full max-w-5xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 flex flex-col max-h-[90vh]">
			<!-- Header -->
			<div class="flex items-start justify-between gap-4 p-6 border-b border-gray-200 dark:border-gray-700">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Riwayat Tindak Lanjut</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
						<span x-text="selected?.number || 'DRAFT'"></span> - <span x-text="selected?.subject"></span>
					</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600">
					<i data-feather="x" class="w-5 h-5"></i>
				</button>
			</div>

			<!-- Content -->
			<div class="overflow-y-auto px-6 py-6 flex-1">
				<template x-if="loadingFollowups">
					<div class="flex items-center justify-center py-12">
						<div class="animate-spin rounded-full h-8 w-8 border-b-2 border-amber-600"></div>
					</div>
				</template>

				<template x-if="!loadingFollowups && followupsList.length === 0">
					<div class="text-center py-12">
						<i data-feather="clipboard" class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-3"></i>
						<p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada tindak lanjut untuk surat tugas ini</p>
						<button @click="closeAll(); open('showFollowup', selected);" class="mt-4 px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium">
							<i data-feather="plus" class="w-4 h-4 inline mr-1"></i>
							Tambah Tindak Lanjut
						</button>
					</div>
				</template>

				<template x-if="!loadingFollowups && followupsList.length > 0">
					<div class="space-y-4">
						<!-- Stats Summary -->
						<div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
							<div class="bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-indigo-900/20 dark:to-blue-900/20 rounded-lg p-4 border border-indigo-200 dark:border-indigo-700">
								<div class="text-xs font-medium text-indigo-600 dark:text-indigo-400 mb-1">Total</div>
								<div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300" x-text="followupsList.length"></div>
							</div>
							<div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-700">
								<div class="text-xs font-medium text-amber-600 dark:text-amber-400 mb-1">Progress</div>
								<div class="text-2xl font-bold text-amber-700 dark:text-amber-300" x-text="followupsList.filter(f => f.type === 'progress').length"></div>
							</div>
							<div class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-900/20 dark:to-green-900/20 rounded-lg p-4 border border-emerald-200 dark:border-emerald-700">
								<div class="text-xs font-medium text-emerald-600 dark:text-emerald-400 mb-1">Selesai</div>
								<div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300" x-text="followupsList.filter(f => f.type === 'completed').length"></div>
							</div>
							<div class="bg-gradient-to-br from-rose-50 to-red-50 dark:from-rose-900/20 dark:to-red-900/20 rounded-lg p-4 border border-rose-200 dark:border-rose-700">
								<div class="text-xs font-medium text-rose-600 dark:text-rose-400 mb-1">Kendala</div>
								<div class="text-2xl font-bold text-rose-700 dark:text-rose-300" x-text="followupsList.filter(f => f.type === 'issue').length"></div>
							</div>
						</div>

						<!-- Timeline -->
						<div class="relative">
							<div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200 dark:bg-gray-700"></div>
							
							<template x-for="(item, index) in followupsList" :key="item.id">
								<div class="relative pl-12 pb-8 group">
									<!-- Timeline dot -->
									<div class="absolute left-0 w-8 h-8 rounded-full flex items-center justify-center shadow-lg"
										:class="{
											'bg-gradient-to-br from-amber-500 to-orange-500': item.type === 'progress',
											'bg-gradient-to-br from-emerald-500 to-green-500': item.type === 'completed',
											'bg-gradient-to-br from-rose-500 to-red-500': item.type === 'issue',
											'bg-gradient-to-br from-indigo-500 to-blue-500': item.type === 'report'
										}">
										<i :data-feather="item.type === 'progress' ? 'activity' : item.type === 'completed' ? 'check-circle' : item.type === 'issue' ? 'alert-circle' : 'file-text'" class="w-4 h-4 text-white"></i>
									</div>

									<!-- Content card -->
									<div class="bg-white dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 p-4 shadow-sm hover:shadow-md transition">
										<div class="flex items-start justify-between gap-3 mb-3">
											<div class="flex-1">
												<div class="flex items-center gap-2 mb-1">
													<span class="px-2 py-0.5 rounded-full text-[10px] font-medium"
														:class="{
															'bg-amber-500/20 text-amber-700 dark:text-amber-300': item.type === 'progress',
															'bg-emerald-500/20 text-emerald-700 dark:text-emerald-300': item.type === 'completed',
															'bg-rose-500/20 text-rose-700 dark:text-rose-300': item.type === 'issue',
															'bg-indigo-500/20 text-indigo-700 dark:text-indigo-300': item.type === 'report'
														}"
														x-text="item.type === 'progress' ? 'Progress' : item.type === 'completed' ? 'Selesai' : item.type === 'issue' ? 'Kendala' : 'Laporan'">
													</span>
													<span class="text-xs text-gray-500 dark:text-gray-400" x-text="item.followup_date"></span>
												</div>
												<h4 class="font-semibold text-gray-800 dark:text-gray-100 text-sm" x-text="item.title"></h4>
											</div>
											<template x-if="item.completion_percentage !== null && item.completion_percentage !== undefined">
												<div class="flex items-center gap-2">
													<div class="text-right">
														<div class="text-xs text-gray-500 dark:text-gray-400">Progress</div>
														<div class="text-lg font-bold"
															:class="{
																'text-rose-600': item.completion_percentage < 30,
																'text-amber-600': item.completion_percentage >= 30 && item.completion_percentage < 70,
																'text-emerald-600': item.completion_percentage >= 70
															}">
															<span x-text="item.completion_percentage"></span>%
														</div>
													</div>
												</div>
											</template>
										</div>

										<p class="text-sm text-gray-600 dark:text-gray-300 mb-3 whitespace-pre-line" x-text="item.description"></p>

										<div class="grid md:grid-cols-2 gap-3 text-xs mb-3">
											<template x-if="item.pic_name">
												<div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
													<i data-feather="user" class="w-3.5 h-3.5 text-gray-400"></i>
													<span x-text="item.pic_name"></span>
												</div>
											</template>
											<template x-if="item.department">
												<div class="flex items-center gap-2 text-gray-600 dark:text-gray-300">
													<i data-feather="briefcase" class="w-3.5 h-3.5 text-gray-400"></i>
													<span x-text="item.department"></span>
												</div>
											</template>
										</div>

										<template x-if="item.notes">
											<div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 mb-3">
												<div class="text-[10px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-1">Catatan</div>
												<p class="text-xs text-gray-600 dark:text-gray-300" x-text="item.notes"></p>
											</div>
										</template>

										<template x-if="item.attachment_path">
											<div class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
												<i data-feather="paperclip" class="w-3.5 h-3.5"></i>
												<a :href="`/storage/${item.attachment_path}`" target="_blank" x-text="item.attachment_name"></a>
											</div>
										</template>

										<div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600 flex items-center justify-between text-[10px] text-gray-400 dark:text-gray-500">
											<span>
												<i data-feather="user" class="w-3 h-3 inline"></i>
												<span x-text="item.created_by_name"></span>
											</span>
											<span x-text="item.created_at"></span>
										</div>
									</div>
								</div>
							</template>
						</div>
					</div>
				</template>
			</div>

			<!-- Footer -->
			<div class="flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700">
				<button @click="closeAll(); open('showFollowup', selected);" class="px-4 py-2 rounded-lg text-sm bg-amber-600 hover:bg-amber-500 text-white font-medium flex items-center gap-2">
					<i data-feather="plus" class="w-4 h-4"></i>
					Tambah Tindak Lanjut
				</button>
				<button @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 font-medium">
					Tutup
				</button>
			</div>
		</div>
	</div>
</template>
