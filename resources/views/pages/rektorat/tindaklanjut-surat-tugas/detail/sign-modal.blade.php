<template x-if="showSign">
	<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
		<div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
		<div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5"
			x-data="signAssignmentModal(selected)" x-init="init()">
			<div class="flex items-start justify-between gap-4">
				<div>
					<h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tanda Tangan Surat Tugas</h3>
					<p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number || selected?.temp"></p>
				</div>
				<button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x" class="w-5 h-5"></i></button>
			</div>
			<div class="space-y-4 text-sm">
				<!-- Tab Selector -->
				<div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
					<button type="button" @click="signMode = 'draw'" 
						:class="signMode === 'draw' ? 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'"
						class="px-4 py-2 text-xs font-medium">
						<i data-feather="edit-3" class="w-3 h-3 inline mr-1"></i> Gambar
					</button>
					<button type="button" @click="signMode = 'upload'" 
						:class="signMode === 'upload' ? 'border-b-2 border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'text-gray-500 dark:text-gray-400'"
						class="px-4 py-2 text-xs font-medium">
						<i data-feather="upload" class="w-3 h-3 inline mr-1"></i> Upload
					</button>
				</div>

				<!-- Draw Mode -->
				<div x-show="signMode === 'draw'"
					class="bg-gray-50 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6">
					<p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Gambar tanda tangan menggunakan mouse</p>
					<canvas x-ref="signaturePad" 
						class="w-full h-32 bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600 cursor-crosshair"
						@mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing"
						@touchstart.prevent="startDrawing" @touchmove.prevent="draw" @touchend.prevent="stopDrawing">
					</canvas>
					<div class="flex justify-end mt-2">
						<button type="button" @click="clearCanvas()" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
							<i data-feather="trash-2" class="w-3 h-3 inline mr-1"></i> Hapus
						</button>
					</div>
				</div>

				<!-- Upload Mode -->
				<div x-show="signMode === 'upload'"
					class="bg-gray-50 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6">
					<p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Upload gambar PNG/JPG (Max 2MB)</p>
					<input type="file" x-ref="signatureFile" @change="handleFileUpload" accept="image/png,image/jpeg,image/jpg" class="hidden" />
					<div class="space-y-3">
						<button type="button" @click="$refs.signatureFile.click()"
							class="w-full px-4 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-emerald-500 transition-colors">
							<i data-feather="upload-cloud" class="w-6 h-6 mx-auto mb-1 text-gray-400"></i>
							<span class="text-xs text-gray-600 dark:text-gray-300">Pilih file</span>
						</button>
						<div x-show="uploadPreview" class="relative">
							<img :src="uploadPreview" class="w-full h-32 object-contain bg-white dark:bg-gray-800 rounded border" />
							<button type="button" @click="clearUpload()" 
								class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600">
								<i data-feather="x" class="w-3 h-3"></i>
							</button>
						</div>
					</div>
				</div>

				<div class="grid grid-cols-2 gap-4">
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Metode</label>
						<select x-model="form.signature_type" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500">
							<option value="digital">Digital</option>
							<option value="electronic">Electronic</option>
						</select>
					</div>
					<div>
						<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Jenis File</label>
						<select class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500">
							<option value="pdf">PDF</option>
							<option value="image">Image</option>
						</select>
					</div>
				</div>
				<div>
					<label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan (opsional)</label>
					<textarea rows="3" x-model="form.notes" class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500 text-gray-700 dark:text-gray-100" placeholder="Catatan tanda tangan..."></textarea>
				</div>
				<div class="flex items-center justify-end gap-3 pt-2">
					<button type="button" @click="closeAll()" class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Batal</button>
					<button type="button" @click="sign()" :disabled="submitting" class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium">
						<span x-show="!submitting">Tandatangani</span>
						<span x-show="submitting">Proses...</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
function signAssignmentModal(sel) {
	return {
		selected: sel,
		submitting: false,
		signMode: 'draw',
		uploadPreview: null,
		uploadFile: null,
		isDrawing: false,
		canvas: null,
		ctx: null,
		form: {
			signature_type: 'digital',
			notes: ''
		},
		init() {
			this.$nextTick(() => {
				this.initCanvas();
				if (window.feather) feather.replace();
			});
		},
		initCanvas() {
			this.canvas = this.$refs.signaturePad;
			if (!this.canvas) return;
			this.ctx = this.canvas.getContext('2d');
			const rect = this.canvas.getBoundingClientRect();
			this.canvas.width = rect.width;
			this.canvas.height = rect.height;
			this.ctx.strokeStyle = '#000';
			this.ctx.lineWidth = 2;
			this.ctx.lineCap = 'round';
			this.ctx.lineJoin = 'round';
		},
		startDrawing(e) {
			this.isDrawing = true;
			const pos = this.getPosition(e);
			this.ctx.beginPath();
			this.ctx.moveTo(pos.x, pos.y);
		},
		draw(e) {
			if (!this.isDrawing) return;
			const pos = this.getPosition(e);
			this.ctx.lineTo(pos.x, pos.y);
			this.ctx.stroke();
		},
		stopDrawing() {
			this.isDrawing = false;
		},
		getPosition(e) {
			const rect = this.canvas.getBoundingClientRect();
			const clientX = e.touches ? e.touches[0].clientX : e.clientX;
			const clientY = e.touches ? e.touches[0].clientY : e.clientY;
			return { x: clientX - rect.left, y: clientY - rect.top };
		},
		clearCanvas() {
			if (this.ctx && this.canvas) {
				this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
			}
		},
		handleFileUpload(e) {
			const file = e.target.files[0];
			if (!file) return;
			if (!file.type.match('image/(png|jpeg|jpg)')) {
				alert('Hanya PNG/JPG');
				return;
			}
			if (file.size > 2 * 1024 * 1024) {
				alert('Max 2MB');
				return;
			}
			this.uploadFile = file;
			const reader = new FileReader();
			reader.onload = (e) => { this.uploadPreview = e.target.result; };
			reader.readAsDataURL(file);
		},
		clearUpload() {
			this.uploadPreview = null;
			this.uploadFile = null;
			if (this.$refs.signatureFile) this.$refs.signatureFile.value = '';
		},
		async sign() {
			this.submitting = true;
			try {
				const formData = new FormData();
				formData.append('signature_type', this.form.signature_type);
				formData.append('notes', this.form.notes || '');
				
				if (this.signMode === 'draw' && this.canvas) {
					formData.append('signature_data', this.canvas.toDataURL('image/png'));
				} else if (this.signMode === 'upload') {
					if (!this.uploadFile) { alert('Pilih file'); this.submitting = false; return; }
					formData.append('signature_file', this.uploadFile);
				}
				
				// Dummy alert for now - replace with actual API call
				alert('Tanda tangan berhasil (fungsi lengkap akan diimplementasikan)');
				this.closeAll();
			} catch (e) {
				console.error(e);
				alert('Gagal');
			} finally {
				this.submitting = false;
			}
		}
	}
}
</script>
