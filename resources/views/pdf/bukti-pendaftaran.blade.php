<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bukti Pendaftaran - {{ $pendaftaran->nomor_pendaftaran }}</title>
    <style>
        @page {
            margin: 15mm;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #2563eb;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18pt;
            color: #1e40af;
            text-transform: uppercase;
        }
        
        .header h2 {
            margin: 4px 0 0 0;
            font-size: 14pt;
            color: #374151;
            font-weight: normal;
        }
        
        .header p {
            margin: 2px 0;
            font-size: 9pt;
            color: #6b7280;
        }
        
        .nomor-pendaftaran {
            background: #eff6ff;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 10px;
            margin: 12px 0;
            text-align: center;
        }
        
        .nomor-pendaftaran strong {
            font-size: 16pt;
            color: #1e40af;
            letter-spacing: 2px;
        }
        
        /* ✅ FOTO DAN DATA PRIBADI DALAM 1 BARIS */
        .data-pribadi-container {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .foto-section {
            display: table-cell;
            width: 120px;
            vertical-align: top;
            padding-right: 15px;
        }
        
        .foto-section img {
            width: 110px;
            height: 140px;
            object-fit: cover;
            border: 3px solid #2563eb;
            border-radius: 8px;
            display: block;
        }
        
        .foto-section .caption {
            text-align: center;
            font-size: 8pt;
            color: #6b7280;
            margin-top: 4px;
        }
        
        .data-section {
            display: table-cell;
            vertical-align: top;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .section-title {
            background: #1e40af;
            color: white;
            padding: 6px 10px;
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
            border-radius: 4px;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .data-table tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .data-table td {
            padding: 5px 4px;
            font-size: 9.5pt;
        }
        
        .data-table td:first-child {
            width: 35%;
            color: #6b7280;
            font-weight: 500;
        }
        
        .data-table td:nth-child(2) {
            width: 5%;
            text-align: center;
        }
        
        .data-table td:last-child {
            width: 60%;
            font-weight: 600;
            color: #111827;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 9pt;
            font-weight: bold;
        }
        
        .status-submitted {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }
        
        .footer {
            margin-top: 20px;
            border-top: 2px solid #e5e7eb;
            padding-top: 12px;
        }
        
        .footer-grid {
            display: table;
            width: 100%;
        }
        
        .footer-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .footer-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .notes {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 8px 12px;
            margin-top: 15px;
            font-size: 8.5pt;
        }
        
        .notes strong {
            color: #92400e;
        }
        
        .notes ul {
            margin: 4px 0;
            padding-left: 18px;
        }
        
        .notes li {
            margin-bottom: 2px;
        }
        
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80pt;
            color: rgba(0, 0, 0, 0.05);
            z-index: -1;
        }
    </style>
</head>
<body>
    {{-- Watermark --}}
    <div class="watermark">SPMB 2027</div>

    {{-- Header --}}
    <div class="header">
        <h1>SMA NEGERI TARUNA NALA JAWA TIMUR</h1>
        <h2>Sistem Penerimaan Murid Baru (SPMB)</h2>
        <p>Tahun Ajaran 2027/2028</p>
        <p>Jl Raya Tlogowaru No. 66 | Telp: (0341) 2993222 | Email: info@smantarunajatim.sch.id</p>
    </div>

    {{-- Nomor Pendaftaran --}}
    <div class="nomor-pendaftaran">
        <p style="margin: 0; font-size: 9pt; color: #6b7280;">Nomor Pendaftaran</p>
        <strong>{{ $pendaftaran->nomor_pendaftaran }}</strong>
    </div>

    {{-- ✅ Data Pribadi dengan Foto dalam 1 Baris --}}
    <div class="section">
        <div class="section-title">A. DATA PRIBADI CALON PESERTA DIDIK</div>
        
        <div class="data-pribadi-container">
            {{-- Foto di kiri --}}
            @if(isset($foto_base64))
            <div class="foto-section">
                <img src="{{ $foto_base64 }}" alt="Pas Foto">
                <div class="caption">Pas Foto 3x4</div>
            </div>
            @endif
            
            {{-- Data di kanan --}}
            <div class="data-section">
                <table class="data-table">
                    <tr>
                        <td>Nama Lengkap</td>
                        <td>:</td>
                        <td>{{ $dataPribadi->nama_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td>{{ $user->nik ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>NISN</td>
                        <td>:</td>
                        <td>{{ $user->nisn ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Kelamin</td>
                        <td>:</td>
                        <td>{{ $dataPribadi->jenis_kelamin ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Tempat, Tanggal Lahir</td>
                        <td>:</td>
                        <td>{{ $dataPribadi->tempat_lahir ?? '-' }}, {{ $dataPribadi->tanggal_lahir ? \Carbon\Carbon::parse($dataPribadi->tanggal_lahir)->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Alamat</td>
                        <td>:</td>
                        <td>{{ $dataPribadi->alamat_lengkap ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. HP / WhatsApp</td>
                        <td>:</td>
                        <td>{{ $dataPribadi->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td>{{ $user->email ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {{-- Data Sekolah Asal --}}
    @if($dataSekolahAsal)
    <div class="section">
        <div class="section-title">C. DATA SEKOLAH ASAL</div>
        <table class="data-table">
            <tr>
                <td>Nama Sekolah</td>
                <td>:</td>
                <td>{{ $dataSekolahAsal->nama_sekolah ?? '-' }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Status Pendaftaran --}}
    <div class="section">
        <div class="section-title">D. STATUS PENDAFTARAN</div>
        <table class="data-table">
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>
                    @if($pendaftaran->status === 'submitted')
                        <span class="status-badge status-submitted">Menunggu Verifikasi</span>
                    @elseif($pendaftaran->status === 'approved')
                        <span class="status-badge status-approved">Disetujui</span>
                    @else
                        <span class="status-badge">{{ ucfirst($pendaftaran->status) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td>Tanggal Submit</td>
                <td>:</td>
                <td>{{ $pendaftaran->submitted_at ? $pendaftaran->submitted_at->format('d F Y, H:i') . ' WIB' : '-' }}</td>
            </tr>
            @if($pendaftaran->verified_at)
            <tr>
                <td>Tanggal Verifikasi</td>
                <td>:</td>
                <td>{{ $pendaftaran->verified_at->format('d F Y, H:i') . ' WIB' }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Notes --}}
    <div class="notes">
        <strong>Catatan Penting:</strong>
        <ul>
            <li>Bukti pendaftaran ini adalah dokumen resmi. Simpan dengan baik.</li>
            <li>Bawa bukti ini saat tes masuk atau verifikasi dokumen.</li>
            <li>Pastikan nomor pendaftaran Anda tercatat dengan benar.</li>
            <li>Hubungi panitia jika ada ketidaksesuaian data.</li>
        </ul>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-grid">
            <div class="footer-left">
                <p style="margin: 0; font-size: 8.5pt; color: #6b7280;">
                    Dicetak pada: {{ $tanggal_cetak }}<br>
                    Dokumen ini digenerate otomatis oleh sistem PPDB Online
                </p>
            </div>
        </div>
    </div>

    {{-- Page number --}}
    <script type="text/php">
        if (isset($pdf)) {
            $text = "Halaman {PAGE_NUM} dari {PAGE_COUNT}";
            $size = 8;
            $font = $fontMetrics->getFont("Arial");
            $width = $fontMetrics->get_text_width($text, $font, $size) / 2;
            $x = ($pdf->get_width() - $width) / 2;
            $y = $pdf->get_height() - 35;
            $pdf->page_text($x, $y, $text, $font, $size);
        }
    </script>
</body>
</html>