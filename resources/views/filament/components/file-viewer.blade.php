@php
    // Support both passing methods: $url directly OR $file that needs to be converted
    $fileUrl = $url ?? ($file ? asset('storage/' . $file) : null);
    $fileExtension = $file ? pathinfo($file, PATHINFO_EXTENSION) : ($fileUrl ? pathinfo(parse_url($fileUrl, PHP_URL_PATH), PATHINFO_EXTENSION) : null);
    $fileType = $type ?? (in_array(strtolower($fileExtension ?? ''), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'pdf');
@endphp

<div>
    @if($fileUrl)
        <div class="space-y-2">
            {{-- Info File --}}
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <div class="flex items-center gap-2">
                    @if($fileType === 'pdf')
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $label ?? 'File PDF' }} Tersedia
                        </span>
                    @else
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $label ?? 'File Gambar' }} Tersedia
                        </span>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ $fileUrl }}" 
                       target="_blank"
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                        Buka
                    </a>
                    <a href="{{ $fileUrl }}" 
                       download
                       class="inline-flex items-center gap-1 px-3 py-1.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </a>
                </div>
            </div>

            {{-- Preview --}}
            @if($fileType === 'pdf')
                {{-- PDF Preview with iframe --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            💡 Tip: Klik "Buka" untuk melihat dalam tab baru jika preview tidak muncul
                        </p>
                    </div>
                    <iframe 
                        src="{{ $fileUrl }}#toolbar=1&navpanes=0&scrollbar=1&view=FitH" 
                        class="w-full border-0"
                        style="height: 600px;"
                        frameborder="0"
                        loading="lazy">
                    </iframe>
                </div>
            @else
                {{-- Image Preview --}}
                <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900">
                    <div class="p-2 bg-gray-100 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            🖼️ Preview {{ $label ?? 'Gambar' }}
                        </p>
                    </div>
                    <div class="flex justify-center p-4 bg-gray-50 dark:bg-gray-800">
                        <img 
                            src="{{ $fileUrl }}" 
                            alt="{{ $label ?? 'Preview' }}"
                            class="max-w-full h-auto rounded-lg shadow-lg"
                            style="max-height: 700px; object-fit: contain;"
                            loading="lazy">
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                📄 {{ $label ?? 'File' }} belum diupload
            </p>
        </div>
    @endif
</div>