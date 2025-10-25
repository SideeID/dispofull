<template x-if="showRoute">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6 max-h-[90vh] overflow-y-auto">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
						<i data-feather="git-commit" class="w-5 h-5 text-violet-600 dark:text-violet-400"></i>
						Alur Disposisi
					</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>

			<div class="grid md:grid-cols-3 gap-6 text-sm">
				<!-- Main Timeline -->
				<div class="md:col-span-2 space-y-4">
					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-5">
						<div class="flex items-center justify-between mb-4">
							<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Timeline Disposisi</h4>
							<span class="text-xs text-gray-500 dark:text-gray-400" x-text="`${steps.length} langkah`"></span>
						</div>
						<div class="space-y-4 text-xs">
							<template x-for="(s,i) in steps" :key="s.time + s.unit + i">
								<div class="flex items-start gap-3">
									<div class="flex flex-col items-center pt-1">
										<div class="w-3 h-3 rounded-full flex-shrink-0" :class="{
											'bg-emerald-500 ring-2 ring-emerald-200 dark:ring-emerald-900': s.status=='success',
											'bg-amber-500 ring-2 ring-amber-200 dark:ring-amber-900': s.status=='pending',
											'bg-blue-500 ring-2 ring-blue-200 dark:ring-blue-900': s.status=='info'
										}"></div>
										<div class="w-0.5 flex-1 bg-gradient-to-b from-gray-300 dark:from-gray-600 to-transparent mt-2" x-show="i < steps.length-1"></div>
									</div>
									<div class="flex-1 pb-4">
										<div class="flex items-center justify-between mb-1">
											<span class="font-semibold text-gray-800 dark:text-gray-100" x-text="s.unit"></span>
											<span class="text-[10px] font-mono px-2 py-0.5 rounded bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300" x-text="s.time"></span>
										</div>
										<div class="text-gray-700 dark:text-gray-200 mb-1" x-text="s.action"></div>
										<div class="flex items-center gap-2">
											<span class="px-2 py-0.5 rounded-full text-[10px] font-medium" :class="{
												'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': s.status=='success',
												'bg-amber-500/10 text-amber-600 dark:text-amber-400': s.status=='pending',
												'bg-blue-500/10 text-blue-600 dark:text-blue-400': s.status=='info'
											}" x-text="s.status=='success' ? 'Selesai' : s.status=='pending' ? 'Menunggu' : 'Dalam Proses'"></span>
										</div>
									</div>
								</div>
							</template>
							<div x-show="!steps || steps.length === 0" class="text-center text-gray-400 py-8">
								<i data-feather="inbox" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
								<p>Belum ada alur disposisi</p>
							</div>
						</div>
					</div>

					<!-- Summary Stats -->
					<div class="grid grid-cols-3 gap-3">
						<div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-lg p-3 text-center">
							<div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400" x-text="steps.filter(s => s.status === 'success').length"></div>
							<div class="text-[10px] text-gray-600 dark:text-gray-400 mt-1">Selesai</div>
						</div>
						<div class="bg-amber-50 dark:bg-amber-900/20 rounded-lg p-3 text-center">
							<div class="text-2xl font-bold text-amber-600 dark:text-amber-400" x-text="steps.filter(s => s.status === 'pending').length"></div>
							<div class="text-[10px] text-gray-600 dark:text-gray-400 mt-1">Menunggu</div>
						</div>
						<div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-3 text-center">
							<div class="text-2xl font-bold text-blue-600 dark:text-blue-400" x-text="steps.filter(s => s.status === 'info').length"></div>
							<div class="text-[10px] text-gray-600 dark:text-gray-400 mt-1">Proses</div>
						</div>
					</div>
				</div>

				<!-- Sidebar Info -->
				<div class="space-y-4">
					<div class="bg-violet-50 dark:bg-violet-900/20 rounded-lg p-4">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
							<i data-feather="info" class="w-4 h-4 text-violet-600 dark:text-violet-400"></i>
							Info Disposisi
						</h4>
						<div class="space-y-2 text-xs">
							<div class="flex justify-between">
								<span class="text-gray-600 dark:text-gray-400">Total Langkah</span>
								<span class="font-semibold text-gray-800 dark:text-gray-100" x-text="steps.length"></span>
							</div>
							<div class="flex justify-between">
								<span class="text-gray-600 dark:text-gray-400">Prioritas</span>
								<span class="px-2 py-0.5 rounded-full text-[10px] font-medium" :class="{
									'bg-emerald-500/10 text-emerald-600': selected?.priority=='low',
									'bg-slate-500/10 text-slate-600': selected?.priority=='normal',
									'bg-amber-500/10 text-amber-600': selected?.priority=='high',
									'bg-rose-500/10 text-rose-600': selected?.priority=='urgent'
								}" x-text="selected?.priority"></span>
							</div>
							<div class="flex justify-between">
								<span class="text-gray-600 dark:text-gray-400">Status Surat</span>
								<span class="px-2 py-0.5 rounded-full text-[10px] font-medium" :class="statusClass(normalizeStatus(selected||{}))" x-text="normalizeStatus(selected||{})"></span>
							</div>
						</div>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-3 flex items-center gap-2">
							<i data-feather="users" class="w-4 h-4 text-gray-600 dark:text-gray-400"></i>
							Unit Terlibat
						</h4>
						<div class="space-y-1.5 max-h-40 overflow-y-auto">
							<template x-for="(unit, idx) in [...new Set(steps.map(s => s.unit))]" :key="'unit-' + idx">
								<div class="flex items-center gap-2 text-xs p-2 rounded bg-white dark:bg-gray-700/60">
									<i data-feather="briefcase" class="w-3 h-3 text-violet-600 dark:text-violet-400"></i>
									<span class="text-gray-800 dark:text-gray-100" x-text="unit"></span>
								</div>
							</template>
						</div>
					</div>

					<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg p-4">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-gray-100 mb-2">Progress</h4>
						<div class="space-y-2">
							<div class="flex justify-between text-xs text-gray-600 dark:text-gray-400">
								<span>Penyelesaian</span>
								<span x-text="`${Math.round((steps.filter(s => s.status === 'success').length / (steps.length || 1)) * 100)}%`"></span>
							</div>
							<div class="h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
								<div class="h-full bg-gradient-to-r from-violet-500 to-purple-600 transition-all duration-500" :style="`width: ${(steps.filter(s => s.status === 'success').length / (steps.length || 1)) * 100}%`"></div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="flex items-center justify-end pt-2 border-t border-gray-200 dark:border-gray-700">
				<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Tutup</button>
			</div>
		</div>
	</div>
</template>
