{{-- resources/views/filament/pendaftar/widgets/informasi-penting-widget.blade.php --}}

<x-filament::widget>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="p-4 sm:p-6">
            {{-- Header --}}
            <div class="flex items-center gap-3 mb-5 pb-3 border-b border-gray-100 dark:border-gray-700/60">
                <div class="p-2.5 bg-amber-100 dark:bg-amber-900/40 rounded-xl shrink-0 text-amber-600 dark:text-amber-400">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">
                    {{ $content['info_heading'] ?? 'Informasi Penting' }}
                </h3>
            </div>

            {{-- List Items --}}
            <div class="space-y-4">
                @foreach (($content['info_items'] ?? []) as $item)
                    @php
                        $rawDesc = trim($item['description'] ?? '');
                        $linkUrl = trim($item['link_url'] ?? '');
                        $linkLabel = trim($item['link_label'] ?? '');

                        // Otomatis ekstrak URL dari deskripsi jika ada
                        if (empty($linkUrl)) {
                            if (preg_match('/(https?:\/\/[^\s<]+)/i', $rawDesc, $matches)) {
                                $linkUrl = $matches[1];
                                // Hapus URL dari teks deskripsi agar rapi dan tidak berantakan
                                $cleanedDesc = trim(str_replace($linkUrl, '', $rawDesc));
                                $cleanedDesc = trim(preg_replace('/^[:\s\-]+|[:\s\-]+$/', '', $cleanedDesc));
                                $rawDesc = $cleanedDesc;
                            }
                        } else {
                            // Jika link_url sudah diisi, hapus URL yang sama di deskripsi jika ada
                            $rawDesc = trim(str_replace($linkUrl, '', $rawDesc));
                            $rawDesc = trim(preg_replace('/^[:\s\-]+|[:\s\-]+$/', '', $rawDesc));
                        }

                        $isWhatsApp = false;
                        if (!empty($linkUrl)) {
                            $isWhatsApp = str_contains($linkUrl, 'chat.whatsapp.com') || str_contains($linkUrl, 'wa.me') || str_contains($linkUrl, 'whatsapp.com');

                            if (empty($linkLabel)) {
                                $linkLabel = $isWhatsApp ? 'Gabung Grup WhatsApp' : 'Buka Tautan';
                            }
                        }
                    @endphp

                    <div class="flex gap-3 items-start">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-blue-100 dark:bg-blue-900/60 text-blue-600 dark:text-blue-400 rounded-full font-bold text-sm mt-0.5">
                            {{ $loop->iteration }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 text-sm sm:text-base mb-1 break-words">
                                {{ $item['title'] ?? '' }}
                            </h4>

                            @if (!empty($rawDesc))
                                <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-2 break-words">
                                    {!! nl2br(e($rawDesc)) !!}
                                </p>
                            @endif

                            {{-- Tombol Link Interaktif --}}
                            @if (!empty($linkUrl))
                                <div class="mt-2 mb-1">
                                    <a href="{{ $linkUrl }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-medium rounded-lg transition-all duration-150 shadow-sm active:scale-95 {{ $isWhatsApp 
                                            ? 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/20' 
                                            : 'bg-primary-600 hover:bg-primary-700 text-white shadow-primary-600/20' }}">
                                        @if ($isWhatsApp)
                                            {{-- WhatsApp SVG Icon --}}
                                            <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        @endif
                                        <span>{{ $linkLabel }}</span>
                                        <svg class="w-3.5 h-3.5 opacity-70 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Kontak Panitia --}}
            @if (!empty($content['contact_phone']) || !empty($content['contact_email']) || !empty($content['contact_whatsapp']))
                <div class="mt-6 p-4 bg-gradient-to-r from-blue-50/80 to-indigo-50/80 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200/80 dark:border-blue-800/80">
                    <div class="flex items-start gap-3">
                        <div class="p-1.5 bg-blue-100 dark:bg-blue-800/50 rounded-lg text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0 text-sm">
                            <p class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $content['contact_heading'] ?? 'Kontak Panitia SPMB:' }}
                            </p>
                            <div class="text-gray-700 dark:text-gray-300 space-y-1.5 text-xs sm:text-sm break-words">
                                @if (!empty($content['contact_phone']))
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">Telepon:</span>
                                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $content['contact_phone']) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $content['contact_phone'] }}
                                        </a>
                                    </div>
                                @endif
                                @if (!empty($content['contact_email']))
                                    <div class="flex items-center gap-1.5 break-all">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">Email:</span>
                                        <a href="mailto:{{ $content['contact_email'] }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $content['contact_email'] }}
                                        </a>
                                    </div>
                                @endif
                                @if (!empty($content['contact_whatsapp']))
                                    @php
                                        $waNum = preg_replace('/[^0-9]/', '', $content['contact_whatsapp']);
                                        if (str_starts_with($waNum, '0')) {
                                            $waNum = '62' . substr($waNum, 1);
                                        }
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-medium text-gray-900 dark:text-gray-100">WhatsApp:</span>
                                        <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener noreferrer" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline inline-flex items-center gap-1">
                                            {{ $content['contact_whatsapp'] }}
                                            <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24">
                                                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766 0-3.187-2.59-5.771-5.764-5.771z"/>
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament::widget>
