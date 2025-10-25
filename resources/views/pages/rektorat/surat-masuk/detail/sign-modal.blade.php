<template x-if="showSign">
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center px-4 py-6 sm:p-0">
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" @click="closeAll()"></div>
        <div class="relative w-full sm:max-w-md bg-white dark:bg-gray-800 rounded-2xl shadow-xl ring-1 ring-gray-200 dark:ring-gray-700 p-6 flex flex-col gap-5"
            x-data="signModal(selected)" x-init="init()">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Tanda Tangan Digital</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="selected?.number"></p>
                </div>
                <button @click="closeAll()" class="text-gray-400 hover:text-rose-600"><i data-feather="x"
                        class="w-5 h-5"></i></button>
            </div>
            <div class="space-y-4 text-sm">
                <!-- Tab Selector -->
                <div class="flex gap-2 border-b border-gray-200 dark:border-gray-700">
                    <button type="button" @click="signMode = 'draw'" 
                        :class="signMode === 'draw' ? 'border-b-2 border-amber-500 text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400'"
                        class="px-4 py-2 text-xs font-medium">
                        <i data-feather="edit-3" class="w-3 h-3 inline mr-1"></i> Gambar Tanda Tangan
                    </button>
                    <button type="button" @click="signMode = 'upload'" 
                        :class="signMode === 'upload' ? 'border-b-2 border-amber-500 text-amber-600 dark:text-amber-400' : 'text-gray-500 dark:text-gray-400'"
                        class="px-4 py-2 text-xs font-medium">
                        <i data-feather="upload" class="w-3 h-3 inline mr-1"></i> Upload Gambar
                    </button>
                </div>

                <!-- Draw Mode -->
                <div x-show="signMode === 'draw'"
                    class="bg-gray-50 dark:bg-gray-700/40 border border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Gambar tanda tangan Anda menggunakan mouse/touchpad</p>
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
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Upload file gambar tanda tangan (PNG, JPG - Max 2MB)</p>
                    <input type="file" x-ref="signatureFile" @change="handleFileUpload" accept="image/png,image/jpeg,image/jpg"
                        class="hidden" />
                    <div class="space-y-3">
                        <button type="button" @click="$refs.signatureFile.click()"
                            class="w-full px-4 py-3 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg hover:border-amber-500 dark:hover:border-amber-500 transition-colors">
                            <i data-feather="upload-cloud" class="w-6 h-6 mx-auto mb-1 text-gray-400"></i>
                            <span class="text-xs text-gray-600 dark:text-gray-300">Klik untuk pilih file</span>
                        </button>
                        <div x-show="uploadPreview" class="relative">
                            <img :src="uploadPreview" class="w-full h-32 object-contain bg-white dark:bg-gray-800 rounded border border-gray-200 dark:border-gray-600" />
                            <button type="button" @click="clearUpload()" 
                                class="absolute top-2 right-2 p-1 bg-red-500 text-white rounded-full hover:bg-red-600">
                                <i data-feather="x" class="w-3 h-3"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label
                            class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Metode</label>
                        <select x-model="form.signature_type"
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="digital">Digital</option>
                            <option value="electronic">Electronic</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Jenis
                            File</label>
                        <select
                            class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-2 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500">
                            <option value="pdf">PDF</option>
                            <option value="image">Image</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[11px] font-medium mb-1 text-gray-600 dark:text-gray-300">Catatan
                        (opsional)</label>
                    <textarea rows="3" x-model="form.notes"
                        class="w-full rounded-lg bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 px-3 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-orange-400 dark:focus:ring-orange-500 text-gray-700 dark:text-gray-100"
                        placeholder="Catatan untuk log..."></textarea>
                </div>
                <div class="border rounded-lg p-3 bg-gray-50 dark:bg-gray-700/40">
                    <div
                        class="text-[11px] uppercase font-semibold tracking-wide text-gray-400 dark:text-gray-500 mb-2">
                        Riwayat Tanda Tangan</div>
                    <ul class="space-y-2 text-[11px] text-gray-600 dark:text-gray-300"
                        x-show="!loading && signatures.length">
                        <template x-for="s in signatures" :key="s.id">
                            <li class="flex items-start justify-between">
                                <div>
                                    <span class="font-medium" x-text="s.user"></span> · <span x-text="s.position || '-'"
                                        class="text-gray-500"></span>
                                    <div class="text-[10px]" x-text="s.signed_at"></div>
                                </div>
                                <span class="text-[10px] px-2 py-0.5 rounded-full"
                                    :class="s.status === 'signed' ? 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' :
                                        'bg-amber-500/10 text-amber-600 dark:text-amber-400'"
                                    x-text="s.status"></span>
                            </li>
                        </template>
                    </ul>
                    <div class="text-center text-[11px] py-4 text-gray-400" x-show="!loading && !signatures.length">
                        Belum ada tanda tangan.</div>
                    <div class="flex items-center justify-center py-4" x-show="loading">
                        <div class="animate-spin h-5 w-5 rounded-full border-2 border-amber-500 border-t-transparent">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="closeAll()"
                        class="px-4 py-2 rounded-lg text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600">Tutup</button>
                    <button type="button" @click="sign()" :disabled="submitting"
                        class="px-4 py-2 rounded-lg text-xs bg-emerald-600 hover:bg-emerald-500 text-white font-medium flex items-center gap-2">
                        <span x-show="!submitting">Tandatangani</span>
                        <span x-show="submitting" class="flex items-center gap-1"><span
                                class="animate-spin h-4 w-4 rounded-full border-2 border-white border-t-transparent"></span>
                            Proses</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    function signModal(sel) {
        return {
            selected: sel,
            signatures: [],
            loading: false,
            submitting: false,
            signMode: 'draw', // 'draw' or 'upload'
            uploadPreview: null,
            uploadFile: null,
            isDrawing: false,
            canvas: null,
            ctx: null,
            form: {
                signature_type: 'digital',
                notes: '',
                signature_data: null,
                signature_file: null
            },
            init() {
                this.fetch();
                this.$nextTick(() => {
                    this.initCanvas();
                });
            },
            initCanvas() {
                this.canvas = this.$refs.signaturePad;
                if (!this.canvas) return;
                this.ctx = this.canvas.getContext('2d');
                
                // Set canvas size
                const rect = this.canvas.getBoundingClientRect();
                this.canvas.width = rect.width;
                this.canvas.height = rect.height;
                
                // Set drawing style
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
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            },
            clearCanvas() {
                if (this.ctx && this.canvas) {
                    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                }
            },
            handleFileUpload(e) {
                const file = e.target.files[0];
                if (!file) return;
                
                // Validate file type
                if (!file.type.match('image/(png|jpeg|jpg)')) {
                    alert('Hanya file PNG atau JPG yang diperbolehkan');
                    return;
                }
                
                // Validate file size (max 2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file maksimal 2MB');
                    return;
                }
                
                this.uploadFile = file;
                
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.uploadPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            },
            clearUpload() {
                this.uploadPreview = null;
                this.uploadFile = null;
                if (this.$refs.signatureFile) {
                    this.$refs.signatureFile.value = '';
                }
            },
            async fetch() {
                if (!this.selected) return;
                this.loading = true;
                try {
                    const r = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/signatures`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });
                    if (r.ok) {
                        const j = await r.json();
                        this.signatures = j.data;
                    }
                } catch (e) {
                    console.error(e);
                } finally {
                    this.loading = false;
                    this.$nextTick(() => {
                        if (window.feather) feather.replace();
                    });
                }
            },
            async sign() {
                this.submitting = true;
                try {
                    const formData = new FormData();
                    formData.append('signature_type', this.form.signature_type);
                    formData.append('notes', this.form.notes || '');
                    
                    if (this.signMode === 'draw') {
                        // Get signature data from canvas
                        if (this.canvas) {
                            const signatureData = this.canvas.toDataURL('image/png');
                            formData.append('signature_data', signatureData);
                        }
                    } else if (this.signMode === 'upload') {
                        // Upload file
                        if (this.uploadFile) {
                            formData.append('signature_file', this.uploadFile);
                        } else {
                            alert('Silakan pilih file tanda tangan terlebih dahulu');
                            this.submitting = false;
                            return;
                        }
                    }
                    
                    const res = await fetch(`/rektor/api/incoming-letters/${this.selected.id}/signatures`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: formData
                    });
                    
                    if (res.ok) {
                        await this.fetch();
                        this.form.notes = '';
                        this.clearCanvas();
                        this.clearUpload();
                        alert('Tanda tangan berhasil disimpan');
                    } else {
                        const err = await res.json();
                        alert(err.message || 'Gagal menyimpan tanda tangan');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Terjadi kesalahan');
                } finally {
                    this.submitting = false;
                }
            }
        }
    }
</script>
