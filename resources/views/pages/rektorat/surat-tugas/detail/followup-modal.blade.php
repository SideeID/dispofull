<template x-if="showFollowup">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-3xl bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-6 overflow-y-auto max-h-[90vh]">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tindak Lanjut Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
						<span x-text="selected?.number || 'DRAFT'"></span> - <span x-text="selected?.subject"></span>
					</p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600">
					<i data-feather="x" class="w-5 h-5"></i>
				</button>
			</div>

			<!-- Informasi Surat Tugas -->
			<div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-lg p-4 border border-amber-200 dark:border-amber-700">
				<div class="grid md:grid-cols-2 gap-4 text-sm">
					<div>
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">Nomor Surat:</span>
						<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="selected?.number || 'DRAFT'"></div>
					</div>
					<div>
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tanggal Surat:</span>
						<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="selected?.date || '-'"></div>
					</div>
					<div class="md:col-span-2">
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">Perihal:</span>
						<div class="font-semibold text-gray-800 dark:text-gray-100" x-text="selected?.subject || '-'"></div>
					</div>
					<div>
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">Periode:</span>
						<div class="text-gray-800 dark:text-gray-100">
							<span x-text="selected?.start || '-'"></span> s/d <span x-text="selected?.end || '-'"></span>
						</div>
					</div>
					<div>
						<span class="text-xs font-medium text-gray-500 dark:text-gray-400">Status Surat:</span>
						<div>
							<span class="px-2.5 py-1 rounded-full text-[11px] font-medium" 
								:class="{
									'bg-slate-500/20 text-slate-700': selected?.status=='draft',
									'bg-amber-500/20 text-amber-700': selected?.status=='pending',
									'bg-emerald-500/20 text-emerald-700': selected?.status=='processed',
									'bg-rose-500/20 text-rose-700': selected?.status=='rejected',
									'bg-gray-500/20 text-gray-700': selected?.status=='closed'
								}"
								x-text="statusLabel(selected?.status)"></span>
						</div>
					</div>
				</div>
			</div>

			<form class="space-y-6 text-sm" @submit.prevent="submitFollowup()">
				<!-- Jenis Tindak Lanjut -->
				<div>
					<label class="block text-xs font-medium mb-2 text-gray-600 dark:text-gray-300">
						Jenis Tindak Lanjut <span class="text-rose-500">*</span>
					</label>
					<div class="grid grid-cols-2 md:grid-cols-4 gap-3">
						<label class="relative flex items-center gap-2 cursor-pointer">
							<input type="radio" x-model="followupForm.type" value="progress" class="peer sr-only" />
							<div class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-amber-500 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 text-center text-xs font-medium transition">
								<i data-feather="activity" class="w-4 h-4 mx-auto mb-1"></i>
								<div>Progress</div>
							</div>
						</label>
						<label class="relative flex items-center gap-2 cursor-pointer">
							<input type="radio" x-model="followupForm.type" value="completed" class="peer sr-only" />
							<div class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 text-center text-xs font-medium transition">
								<i data-feather="check-circle" class="w-4 h-4 mx-auto mb-1"></i>
								<div>Selesai</div>
							</div>
						</label>
						<label class="relative flex items-center gap-2 cursor-pointer">
							<input type="radio" x-model="followupForm.type" value="issue" class="peer sr-only" />
							<div class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-900/20 text-center text-xs font-medium transition">
								<i data-feather="alert-circle" class="w-4 h-4 mx-auto mb-1"></i>
								<div>Kendala</div>
							</div>
						</label>
						<label class="relative flex items-center gap-2 cursor-pointer">
							<input type="radio" x-model="followupForm.type" value="report" class="peer sr-only" />
							<div class="w-full px-3 py-2 rounded-lg border-2 border-gray-200 dark:border-gray-600 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 text-center text-xs font-medium transition">
								<i data-feather="file-text" class="w-4 h-4 mx-auto mb-1"></i>
								<div>Laporan</div>
							</div>
						</label>
					</div>
				</div>

				<div class="grid md:grid-cols-2 gap-4">
					<!-- Tanggal Tindak Lanjut -->
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
							Tanggal Tindak Lanjut <span class="text-rose-500">*</span>
						</label>
						<input 
							type="date" 
							x-model="followupForm.followup_date" 
							required
							class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100" 
						/>
					</div>

					<!-- Persentase Penyelesaian -->
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
							Persentase Penyelesaian
						</label>
						<div class="relative">
							<input 
								type="number" 
								x-model="followupForm.completion_percentage" 
								min="0" 
								max="100" 
								class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 pr-8 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100" 
								placeholder="0-100"
							/>
							<span class="absolute right-3 top-2.5 text-gray-400 text-sm">%</span>
						</div>
						<div class="mt-2 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
							<div 
								class="h-2 rounded-full transition-all duration-300"
								:class="{
									'bg-rose-500': followupForm.completion_percentage < 30,
									'bg-amber-500': followupForm.completion_percentage >= 30 && followupForm.completion_percentage < 70,
									'bg-emerald-500': followupForm.completion_percentage >= 70
								}"
								:style="`width: ${followupForm.completion_percentage || 0}%`"
							></div>
						</div>
					</div>
				</div>

				<!-- Judul Tindak Lanjut -->
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
						Judul Tindak Lanjut <span class="text-rose-500">*</span>
					</label>
					<input 
						type="text" 
						x-model="followupForm.title" 
						required
						class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100" 
						placeholder="Misal: Laporan Pelaksanaan Kegiatan, Update Progress, dll."
					/>
				</div>

				<!-- Deskripsi/Keterangan -->
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
						Deskripsi / Keterangan <span class="text-rose-500">*</span>
					</label>
					<textarea 
						rows="6" 
						x-model="followupForm.description" 
						required
						class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100" 
						placeholder="Jelaskan detail tindak lanjut yang dilakukan, hasil yang dicapai, kendala yang dihadapi, atau rekomendasi..."
					></textarea>
					<p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
						<i data-feather="info" class="w-3 h-3 inline"></i> 
						Jelaskan secara detail aktivitas yang dilakukan, hasil yang dicapai, dan rencana selanjutnya
					</p>
				</div>

				<div class="grid md:grid-cols-2 gap-4">
					<!-- Penanggung Jawab -->
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
							Penanggung Jawab
						</label>
						<input 
							type="text" 
							x-model="followupForm.pic_name" 
							class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100" 
							placeholder="Nama penanggung jawab"
						/>
					</div>

					<!-- Unit/Bagian -->
					<div>
						<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
							Unit / Bagian
						</label>
						<select 
							x-model="followupForm.department" 
							class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100"
						>
							<option value="">Pilih Unit/Departemen</option>
							<template x-for="dept in availableDepartments" :key="dept.id">
								<option :value="dept.name" x-text="dept.name"></option>
							</template>
						</select>
					</div>
				</div>

				<!-- Upload Lampiran -->
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
						Lampiran Pendukung
					</label>
					<div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center hover:border-amber-400 dark:hover:border-amber-500 transition">
						<input 
							type="file" 
							x-ref="followupFileInput"
							@change="followupForm.attachment = $refs.followupFileInput.files[0]"
							accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
							class="block w-full text-xs text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 dark:file:bg-amber-500/10 dark:file:text-amber-300" 
						/>
						<p class="mt-2 text-xs text-gray-400 dark:text-gray-500">
							<i data-feather="upload" class="w-3 h-3 inline"></i>
							PDF, DOC, XLS, atau Gambar (max 10MB)
						</p>
					</div>
					<template x-if="followupForm.attachment">
						<div class="mt-2 flex items-center gap-2 text-xs text-gray-600 dark:text-gray-300 bg-amber-50 dark:bg-amber-900/20 rounded-lg p-2">
							<i data-feather="paperclip" class="w-4 h-4 text-amber-600"></i>
							<span x-text="followupForm.attachment.name"></span>
							<button type="button" @click="followupForm.attachment = null; $refs.followupFileInput.value = ''" class="ml-auto text-rose-500 hover:underline">
								Hapus
							</button>
						</div>
					</template>
				</div>

				<!-- Catatan Tambahan -->
				<div>
					<label class="block text-xs font-medium mb-1 text-gray-600 dark:text-gray-300">
						Catatan Tambahan
					</label>
					<textarea 
						rows="3" 
						x-model="followupForm.notes" 
						class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 text-gray-700 dark:text-gray-100" 
						placeholder="Catatan internal, rekomendasi, atau informasi tambahan lainnya..."
					></textarea>
				</div>

				<div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
					<button 
						type="button" 
						@click="closeAll()" 
						class="px-4 py-2 rounded-lg text-sm bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 font-medium"
					>
						Batal
					</button>
					<button 
						type="submit" 
						:disabled="submittingFollowup" 
						class="px-5 py-2 rounded-lg text-sm bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-500 hover:to-orange-500 text-white font-medium shadow-lg shadow-amber-500/30 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
					>
						<i data-feather="send" class="w-4 h-4" x-show="!submittingFollowup"></i>
						<span x-show="!submittingFollowup">Kirim Tindak Lanjut</span>
						<span x-show="submittingFollowup">Mengirim...</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</template>
