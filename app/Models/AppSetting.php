<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Throwable;

class AppSetting extends Model
{
    public const KEY_REGISTRATION_MODE = 'registration_mode';

    public const KEY_REGISTER_CLOSED_DATE = 'register_closed_date';

    public const KEY_LANDING_PAGE = 'landing_page';

    public const KEY_PENDAFTAR_DASHBOARD = 'pendaftar_dashboard';

    public const MODE_SCHEDULE = 'schedule';

    public const MODE_OPEN = 'open';

    public const MODE_CLOSED = 'closed';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * In-request memoized values to avoid repeated file cache reads per request.
     *
     * @var array<string, mixed>
     */
    protected static array $requestCache = [];

    /**
     * Retrieve a setting value with caching and safe fallback when table is unavailable.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, static::$requestCache)) {
            $entry = static::$requestCache[$key];

            return $entry['exists'] ? $entry['value'] : $default;
        }

        try {
            $cached = Cache::remember("app_setting:{$key}", 300, function () use ($key) {
                $record = static::query()->where('key', $key)->first();

                return $record ? ['exists' => true, 'value' => $record->value] : ['exists' => false, 'value' => null];
            });

            static::$requestCache[$key] = $cached;

            return $cached['exists'] ? $cached['value'] : $default;
        } catch (Throwable) {
            return $default;
        }
    }

    /**
     * Persist a setting value and invalidate its cache entry.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $stringValue = $value === null ? null : (string) $value;

        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $stringValue],
        );

        static::$requestCache[$key] = ['exists' => true, 'value' => $stringValue];
        Cache::forget("app_setting:{$key}");
    }

    /**
     * Get the active registration mode ('schedule', 'open', or 'closed').
     */
    public static function getRegistrationMode(): string
    {
        $mode = static::getValue(self::KEY_REGISTRATION_MODE, self::MODE_SCHEDULE);

        return in_array($mode, [self::MODE_SCHEDULE, self::MODE_OPEN, self::MODE_CLOSED], true)
            ? $mode
            : self::MODE_SCHEDULE;
    }

    /**
     * Resolve the configured registration closing datetime, falling back to config/app.php.
     */
    public static function getRegisterClosedDate(): ?Carbon
    {
        $sentinel = '__UNSET__';
        $raw = static::getValue(self::KEY_REGISTER_CLOSED_DATE, $sentinel);

        if ($raw === $sentinel) {
            $raw = config('app.register_closed_date');
        }

        if (blank($raw)) {
            return null;
        }

        return Carbon::parse($raw);
    }

    /**
     * Determine whether student registration and form editing are currently closed.
     */
    public static function isRegistrationClosed(): bool
    {
        $mode = static::getRegistrationMode();

        if ($mode === self::MODE_OPEN) {
            return false;
        }

        if ($mode === self::MODE_CLOSED) {
            return true;
        }

        $closedDate = static::getRegisterClosedDate();

        return $closedDate !== null && Carbon::now()->gte($closedDate);
    }

    /**
     * Build a localized closing message for alerts and notifications.
     */
    public static function getClosedMessage(): string
    {
        $closedDate = static::getRegisterClosedDate();

        if (static::getRegistrationMode() === self::MODE_CLOSED && (! $closedDate || Carbon::now()->lt($closedDate))) {
            return 'Pendaftaran saat ini sedang ditutup oleh panitia. Anda hanya dapat mengakses dashboard.';
        }

        if (! $closedDate) {
            return 'Pendaftaran saat ini sedang ditutup. Anda hanya dapat mengakses dashboard.';
        }

        return 'Pendaftaran telah ditutup sejak '
            . $closedDate->locale('id')->translatedFormat('d F Y \p\u\k\u\l H:i')
            . '. Anda hanya dapat mengakses dashboard.';
    }

    /**
     * Default editable content for the public landing page.
     */
    public static function defaultLandingContent(): array
    {
        return [
            'school_name' => 'SMAN Taruna Nala Jawa Timur',
            'site_subtitle' => 'Sistem Penerimaan Murid Baru (SPMB)',
            'academic_year' => 'Tahun Ajaran 2027/2028',
            'hero_badge' => 'Penerimaan Peserta Didik Baru',
            'hero_title' => 'Wujudkan Masa Depan Pemimpin Berkarakter di SMAN Taruna Nala',
            'hero_description' => 'Daftar secara online, lengkapi data diri, nilai raport, dan dokumen pendukung, lalu pantau hasil verifikasi serta pengumuman seleksi langsung dari portal pendaftar.',
            'primary_button_text' => 'Daftar Sekarang',
            'secondary_button_text' => 'Masuk Portal Pendaftar',
            'hero_image' => null,
            'announcement_enabled' => true,
            'announcement_text' => 'Pastikan menggunakan alamat email Gmail aktif milik siswa sendiri saat mendaftar dan periksa kembali kelengkapan berkas sebelum menekan tombol Selesaikan Pendaftaran.',
            'timeline_title' => 'Alur & Tahapan Pendaftaran',
            'timeline_subtitle' => 'Ikuti empat langkah mudah berikut untuk menyelesaikan proses pendaftaran secara online.',
            'timeline_items' => [
                [
                    'step' => '01',
                    'title' => 'Registrasi Akun Pendaftar',
                    'date_label' => 'Tahap Awal',
                    'description' => 'Buat akun pendaftar menggunakan NIK, Nomor KK, NISN, dan email Gmail aktif.',
                ],
                [
                    'step' => '02',
                    'title' => 'Lengkapi Formulir Data',
                    'date_label' => 'Pengisian Berkas',
                    'description' => 'Isi Data Pribadi, Data Orang Tua, Sekolah Asal, Nilai Raport semester 1–4, serta Prestasi & Organisasi.',
                ],
                [
                    'step' => '03',
                    'title' => 'Verifikasi Berkas Panitia',
                    'date_label' => 'Proses Seleksi',
                    'description' => 'Panitia memeriksa validitas data. Jika ada catatan revisi, perbaiki langsung melalui dashboard pendaftar.',
                ],
                [
                    'step' => '04',
                    'title' => 'Cetak Bukti & Pengumuman',
                    'date_label' => 'Tahap Akhir',
                    'description' => 'Unduh bukti pendaftaran dan pantau pengumuman hasil seleksi Tahap 1 maupun Tahap 2.',
                ],
            ],
            'info_title' => 'Persyaratan & Dokumen Penting',
            'info_description' => 'Siapkan data dan dokumen berikut sebelum memulai pengisian formulir pendaftaran:',
            'requirements' => [
                [
                    'title' => 'Identitas Kependudukan & Sekolah',
                    'detail' => 'NIK, Nomor Kartu Keluarga (KK), NISN aktif, dan data NPSN sekolah asal.',
                ],
                [
                    'title' => 'Akun Gmail Aktif Siswa',
                    'detail' => 'Digunakan untuk login portal pendaftar, notifikasi status, dan pemulihan kata sandi.',
                ],
                [
                    'title' => 'Data Nilai Raport Semester 1–4',
                    'detail' => 'Nilai Matematika, Bahasa Indonesia, dan Bahasa Inggris dari semester 1 hingga semester 4.',
                ],
                [
                    'title' => 'Pas Foto & Dokumen Pendukung',
                    'detail' => 'Pas foto terbaru serta sertifikat prestasi atau surat keterangan organisasi (jika ada).',
                ],
            ],
            'contact_title' => 'Pusat Informasi & Kontak Panitia',
            'contact_address' => 'Jl. Raya Tlogowaru No. 66, Kedungkandang, Kota Malang, Jawa Timur',
            'contact_phone' => '(0341) 751120',
            'contact_email' => 'spmb@smantarunanala.sch.id',
            'contact_whatsapp' => '',
            'footer_text' => 'SMAN Taruna Nala Jawa Timur — Sistem Penerimaan Murid Baru.',
        ];
    }

    /**
     * Retrieve landing page content merged with defaults.
     */
    public static function getLandingContent(): array
    {
        return static::getJsonSetting(
            self::KEY_LANDING_PAGE,
            static::defaultLandingContent(),
            ['timeline_items', 'requirements']
        );
    }

    /**
     * Save landing page content JSON and clear cache.
     */
    public static function setLandingContent(array $content): void
    {
        static::setJsonSetting(self::KEY_LANDING_PAGE, $content);
    }

    /**
     * Restore landing page content back to defaults.
     */
    public static function resetLandingContent(): void
    {
        static::resetJsonSetting(self::KEY_LANDING_PAGE);
    }

    /**
     * Default editable announcement and info content for the pendaftar dashboard.
     */
    public static function defaultPendaftarDashboardContent(): array
    {
        return [
            'announcement_enabled' => true,
            'announcement_type' => 'info',
            'announcement_title' => 'Pengumuman Panitia SPMB',
            'announcement_body' => 'Selamat datang di Portal Pendaftar SPMB. Mohon lengkapi seluruh data dan berkas pendaftaran dengan teliti sebelum batas waktu penutupan berakhir.',
            'show_info_when_closed' => true,
            'info_heading' => 'Informasi Penting',
            'info_items' => [
                [
                    'title' => 'Lengkapi Data Anda',
                    'description' => 'Pastikan semua data yang Anda isi sudah benar dan lengkap. Data yang tidak lengkap atau tidak valid akan diminta untuk diperbaiki oleh verifikator.',
                ],
                [
                    'title' => 'Cek Status Secara Berkala',
                    'description' => 'Jika status pendaftaran Anda berubah menjadi "Perlu Perbaikan", silakan perbaiki data sesuai catatan dari verifikator.',
                ],
                [
                    'title' => 'Download Bukti Pendaftaran',
                    'description' => 'Setelah pendaftaran disetujui, jangan lupa download dan simpan bukti pendaftaran Anda. Bukti ini akan dibutuhkan untuk proses selanjutnya.',
                ],
                [
                    'title' => 'Hubungi Panitia Jika Ada Kendala',
                    'description' => 'Jika mengalami kesulitan atau ada pertanyaan, hubungi panitia SPMB melalui kontak yang tersedia.',
                ],
            ],
            'contact_heading' => 'Kontak Panitia SPMB:',
            'contact_phone' => '0341-2993222',
            'contact_email' => 'info@smantarunajatim.sch.id',
            'contact_whatsapp' => '081313085908',
        ];
    }

    /**
     * Retrieve pendaftar dashboard announcement & info content merged with defaults.
     */
    public static function getPendaftarDashboardContent(): array
    {
        return static::getJsonSetting(
            self::KEY_PENDAFTAR_DASHBOARD,
            static::defaultPendaftarDashboardContent(),
            ['info_items']
        );
    }

    /**
     * Save pendaftar dashboard announcement & info JSON and clear cache.
     */
    public static function setPendaftarDashboardContent(array $content): void
    {
        static::setJsonSetting(self::KEY_PENDAFTAR_DASHBOARD, $content);
    }

    /**
     * Restore pendaftar dashboard announcement & info back to defaults.
     */
    public static function resetPendaftarDashboardContent(): void
    {
        static::resetJsonSetting(self::KEY_PENDAFTAR_DASHBOARD);
    }

    /**
     * Read and merge a JSON-encoded setting array with defaults.
     */
    protected static function getJsonSetting(string $key, array $defaults, array $listKeys = []): array
    {
        $raw = static::getValue($key);

        if (blank($raw)) {
            return $defaults;
        }

        $decoded = json_decode((string) $raw, true);

        if (! is_array($decoded)) {
            return $defaults;
        }

        $merged = array_merge($defaults, $decoded);

        foreach ($listKeys as $listKey) {
            if (! isset($decoded[$listKey]) || ! is_array($decoded[$listKey])) {
                $merged[$listKey] = $defaults[$listKey];
            } else {
                $merged[$listKey] = array_values($decoded[$listKey]);
            }
        }

        return $merged;
    }

    /**
     * Persist an array setting as JSON.
     */
    protected static function setJsonSetting(string $key, array $content): void
    {
        static::setValue($key, json_encode($content, JSON_UNESCAPED_UNICODE));
    }

    /**
     * Delete a JSON setting and clear its cache entry.
     */
    protected static function resetJsonSetting(string $key): void
    {
        static::query()->where('key', $key)->delete();
        unset(static::$requestCache[$key]);
        Cache::forget("app_setting:{$key}");
    }
}
