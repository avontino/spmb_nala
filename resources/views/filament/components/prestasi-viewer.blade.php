<div>
    @if($prestasi && $prestasi->count() > 0)
        <div class="space-y-4">
            @foreach($prestasi as $index => $item)
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                    {{-- Header --}}
                    <div class="px-4 py-3 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-gray-800 dark:to-gray-900 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-10 h-10 bg-yellow-500 text-white rounded-full font-bold">
                                    {{ $item->urutan ?? $index + 1 }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $item->nama_prestasi }}
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ $item->tingkat_prestasi }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                @if($item->file_bukti_prestasi)
                                    @php
                                        $fileUrl = asset('storage/' . $item->file_bukti_prestasi);
                                        $isPdf = str_ends_with(strtolower($item->file_bukti_prestasi), '.pdf');
                                    @endphp
                                    
                                    <a href="{{ $fileUrl }}" 
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                        </svg>
                                        Buka
                                    </a>
                                    
                                    <a href="{{ $fileUrl }}" 
                                       download
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                        </svg>
                                        Download
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    @if($item->file_bukti_prestasi)
                        @php
                            $fileUrl = asset('storage/' . $item->file_bukti_prestasi);
                            $isPdf = str_ends_with(strtolower($item->file_bukti_prestasi), '.pdf');
                        @endphp

                        <div class="p-4">
                            @if($isPdf)
                                {{-- PDF Preview --}}
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-2">
                                    <iframe 
                                        src="{{ $fileUrl }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH" 
                                        class="w-full border-0 rounded"
                                        style="height: 400px;"
                                        frameborder="0"
                                        loading="lazy">
                                    </iframe>
                                </div>
                            @else
                                {{-- Image Preview --}}
                                <div class="flex justify-center bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                                    <img 
                                        src="{{ $fileUrl }}" 
                                        alt="{{ $item->nama_prestasi }}"
                                        class="max-w-full h-auto rounded-lg shadow-lg"
                                        style="max-height: 500px; object-fit: contain;"
                                        loading="lazy">
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                            <p class="text-sm">📄 Tidak ada bukti prestasi</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="p-6 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
            <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
                    🏆 Belum ada prestasi yang ditambahkan
                </p>
            </div>
        </div>
    @endif
</div>
