<div x-data="pdfViewer('{{ $url }}')" x-init="init()">
    @if($url)
        <div class="space-y-3">
            {{-- Header Controls --}}
            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-900 rounded-lg border border-blue-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                Dokumen PDF
                            </p>
                            <p class="text-xs text-gray-600 dark:text-gray-400" x-show="totalPages > 0">
                                Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex gap-2">
                    {{-- Navigation Controls --}}
                    <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-lg p-1 shadow-sm" x-show="totalPages > 1">
                        <button 
                            @click="prevPage()"
                            :disabled="currentPage === 1"
                            class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                            title="Halaman Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        
                        <input 
                            type="number" 
                            x-model.number="currentPage"
                            @change="goToPage($event.target.value)"
                            min="1"
                            :max="totalPages"
                            class="w-12 text-center text-sm border-0 focus:ring-1 focus:ring-blue-500 rounded dark:bg-gray-700 dark:text-white">
                        
                        <button 
                            @click="nextPage()"
                            :disabled="currentPage === totalPages"
                            class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                            title="Halaman Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Zoom Controls --}}
                    <div class="flex items-center gap-1 bg-white dark:bg-gray-800 rounded-lg p-1 shadow-sm">
                        <button 
                            @click="zoomOut()"
                            class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            title="Zoom Out">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"/>
                            </svg>
                        </button>
                        
                        <span class="text-xs font-medium px-2 dark:text-white" x-text="Math.round(scale * 100) + '%'"></span>
                        
                        <button 
                            @click="zoomIn()"
                            class="p-1.5 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                            title="Zoom In">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v6m3-3H7"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Action Buttons --}}
                    <a href="{{ $url }}" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Buka
                    </a>
                    
                    <a href="{{ $url }}" 
                       download
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>
            </div>

            {{-- Loading State --}}
            <div x-show="loading" class="flex items-center justify-center p-12 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="text-center">
                    <svg class="animate-spin h-10 w-10 text-blue-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Memuat PDF...</p>
                </div>
            </div>

            {{-- Error State --}}
            <div x-show="error" class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800 dark:text-red-400">Gagal memuat PDF</p>
                        <p class="text-xs text-red-600 dark:text-red-500 mt-1">Silakan klik tombol "Buka" untuk melihat di tab baru</p>
                    </div>
                </div>
            </div>

            {{-- PDF Canvas Container --}}
            <div x-show="!loading && !error" 
                 class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-900">
                <div class="overflow-auto" style="max-height: 700px;">
                    <div class="flex justify-center p-4">
                        <canvas 
                            x-ref="canvas"
                            class="shadow-lg max-w-full"
                            style="background: white;">
                        </canvas>
                    </div>
                </div>
            </div>

            {{-- Fallback iframe (jika PDF.js tidak tersedia) --}}
            <div x-show="useFallback && !loading" 
                 class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                <iframe 
                    src="{{ $url }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH" 
                    class="w-full border-0"
                    style="height: 700px;"
                    frameborder="0"
                    loading="lazy">
                </iframe>
            </div>
        </div>
    @else
        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
            <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    📄 Belum ada dokumen yang diupload
                </p>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    function pdfViewer(url) {
        return {
            url: url,
            pdfDoc: null,
            currentPage: 1,
            totalPages: 0,
            scale: 1.5,
            loading: true,
            error: false,
            useFallback: false,

            async init() {
                if (!url) {
                    this.loading = false;
                    return;
                }

                // Check if PDF.js is available
                if (typeof pdfjsLib === 'undefined') {
                    console.warn('PDF.js not loaded, using iframe fallback');
                    this.useFallback = true;
                    this.loading = false;
                    return;
                }

                try {
                    // Set worker
                    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                    
                    // Load PDF
                    const loadingTask = pdfjsLib.getDocument(url);
                    this.pdfDoc = await loadingTask.promise;
                    this.totalPages = this.pdfDoc.numPages;
                    
                    await this.renderPage(this.currentPage);
                    this.loading = false;
                } catch (err) {
                    console.error('Error loading PDF:', err);
                    this.error = true;
                    this.loading = false;
                }
            },

            async renderPage(pageNum) {
                try {
                    const page = await this.pdfDoc.getPage(pageNum);
                    const viewport = page.getViewport({ scale: this.scale });
                    
                    const canvas = this.$refs.canvas;
                    const context = canvas.getContext('2d');
                    
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;
                    
                    const renderContext = {
                        canvasContext: context,
                        viewport: viewport
                    };
                    
                    await page.render(renderContext).promise;
                } catch (err) {
                    console.error('Error rendering page:', err);
                }
            },

            async nextPage() {
                if (this.currentPage < this.totalPages) {
                    this.currentPage++;
                    await this.renderPage(this.currentPage);
                }
            },

            async prevPage() {
                if (this.currentPage > 1) {
                    this.currentPage--;
                    await this.renderPage(this.currentPage);
                }
            },

            async goToPage(pageNum) {
                pageNum = parseInt(pageNum);
                if (pageNum >= 1 && pageNum <= this.totalPages) {
                    this.currentPage = pageNum;
                    await this.renderPage(this.currentPage);
                }
            },

            async zoomIn() {
                this.scale += 0.25;
                await this.renderPage(this.currentPage);
            },

            async zoomOut() {
                if (this.scale > 0.5) {
                    this.scale -= 0.25;
                    await this.renderPage(this.currentPage);
                }
            }
        }
    }
</script>
@endpush
