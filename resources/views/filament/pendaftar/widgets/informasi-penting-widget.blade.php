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

                        $btnBg = $isWhatsApp ? '#25D366' : '#2563eb';
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

                            {{-- Tombol Link Interaktif dengan Inline Style Anti-Purge --}}
                            @if (!empty($linkUrl))
                                <div class="mt-2 mb-1">
                                    <a href="{{ $linkUrl }}" target="_blank" rel="noopener noreferrer"
                                       style="background-color: {{ $btnBg }} !important; color: #ffffff !important; text-decoration: none !important; display: inline-flex !important; align-items: center !important; gap: 8px !important; padding: 9px 16px !important; border-radius: 10px !important; font-weight: 600 !important; font-size: 13px !important; line-height: 1.25 !important; box-shadow: 0 2px 6px rgba(0,0,0,0.12) !important; border: none !important;"
                                       class="transition-transform active:scale-95 hover:opacity-90">
                                        @if ($isWhatsApp)
                                            {{-- WhatsApp SVG Icon --}}
                                            <svg style="width: 17px; height: 17px; fill: #ffffff !important; flex-shrink: 0;" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                            </svg>
                                        @else
                                            <svg style="width: 17px; height: 17px; stroke: #ffffff !important; flex-shrink: 0;" fill="none" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                            </svg>
                                        @endif
                                        <span style="color: #ffffff !important; font-weight: 600 !important; font-size: 13px !important; letter-spacing: 0.01em !important;">{{ $linkLabel }}</span>
                                        <svg style="width: 13px; height: 13px; stroke: #ffffff !important; opacity: 0.85; flex-shrink: 0;" fill="none" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
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
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
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
