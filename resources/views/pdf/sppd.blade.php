<!DOCTYPE html>
<html>

<head>
    <title>SPPD - {{ $visit->pembimbing->teacher->name ?? $visit->pembimbing->name }}</title>
    <style>
        /* MARGIN ATAS GW LEBARIN JADI 230px BIAR KONTEN UTAMA KAGA NABRAK KOP SURAT! */
        @page {
            margin: 230px 40px 40px 40px;
        }

        /* HEADER DITARIK KE ATAS MENGISI AREA MARGIN */
        header {
            position: fixed;
            top: -200px;
            left: 0px;
            right: 0px;
            height: 170px;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.3;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 4px double #000;
            padding-bottom: 3px;
            margin-bottom: 0;
        }

        .header-table td {
            vertical-align: middle;
        }

        .header-text {
            text-align: center;
            padding: 0;
            line-height: 1.05;
        }

        .header-text h3 {
            margin: 0;
            font-size: 11pt;
            font-weight: bold;
        }

        .header-text h2 {
            margin: 3px 0;
            font-size: 16pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header-text p {
            margin: 0;
            font-size: 9.5pt;
        }

        .center-title {
            text-align: center;
            margin-bottom: 20px;
        }

        .center-title span.title-main {
            text-decoration: underline;
            font-weight: bold;
            font-size: 14pt;
        }

        .content-table {
            width: 100%;
        }

        .content-table td {
            vertical-align: top;
            padding: 3px 0;
        }

        .bordered-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .bordered-table th,
        .bordered-table td {
            border: 1px solid #000;
            padding: 6px;
            vertical-align: top;
        }

        .ttd-box {
            width: 100%;
            margin-top: 30px;
        }

        .page-break {
            page-break-after: always;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            height: 25px;
        }
    </style>
</head>

<body>

    <header>
        <table class="header-table">
            <tr>
                <td style="width: 15%; text-align: left; padding-left: 5px;">
                    @if (!empty($school_logo))
                        <img src="{{ $school_logo }}" style="width: 115px; height: auto;">
                    @endif
                </td>
                <td style="width: 70%;" class="header-text">
                    <h3>{{ $yayasan_name ?? 'YAYASAN PEMBINA LEMBAGA PENDIDIKAN DASAR DAN MENENGAH PGRI KABUPATEN KARAWANG' }}
                    </h3>
                    <h2>{{ strtoupper($school_name ?? 'SMK PGRI TELAGASARI') }}</h2>
                    <p>Jurusan : Teknik Pemeliharaan Mekanik Industri, Teknik Pemesinan</p>
                    <p>Teknik Pengelasan, Teknik Kendaraan Ringan, Rekayasa Perangkat Lunak</p>
                    <p><b>( TERAKREDITASI {{ $accreditation ?? 'A' }} )</b></p>
                    <p><b>NPSN : {{ $npsn ?? '20217795' }} NSS : {{ $nss ?? '314022111134' }}</b></p>
                    <p>{{ $school_address ?? 'Jl. Syech Quro Telagasari Desa Talagasari Kec. Telagasari Kab. Karawang 41381' }}
                    </p>
                    <p style="font-size: 8.5pt;">Tlp. {{ $school_phone ?? '(0267) 8622008' }} Email : <span
                            style="color: blue; text-decoration: underline;">{{ $support_email ?? 'info@smkpgritelagasari1.sch.id' }}</span>
                        Website : <span
                            style="color: blue; text-decoration: underline;">{{ $school_website ?? 'https://smkpgritelagasari1.sch.id' }}</span>
                    </p>
                </td>
                <td style="width: 15%; text-align: center; padding-right: 5px; vertical-align: middle;">
                    <img src="data:image/jpeg;base64,{{ base64_encode(@file_get_contents(public_path('images/iqs-image.jpg'))) }}"
                        style="width: 45px; margin-bottom: 5px;"><br>
                    <img src="data:image/png;base64,{{ base64_encode(@file_get_contents(public_path('images/kan-image.png'))) }}"
                        style="width: 85px;">
                </td>
            </tr>
        </table>
    </header>

    <main>
        <div class="center-title">
            <span class="title-main">SURAT TUGAS</span><br>
            <span>Nomor: 30/SATDIK-SMK/II.03/G.{{ date('Y') }}</span>
        </div>

        <p>Yang bertanda tangan dibawah ini:</p>
        <table class="content-table" style="margin-left: 20px;">
            <tr>
                <td width="25%">Nama</td>
                <td width="3%">:</td>
                <td><strong>{{ !empty($kepsek_name) ? $kepsek_name : 'Kepala Sekolah ' . ($school_name ?? 'SMK PGRI TELAGASARI') }}</strong>
                </td>
            </tr>
            <tr>
                <td>NKRS/NIP</td>
                <td>:</td>
                <td>{{ !empty($kepsek_nip) ? $kepsek_nip : '-' }}</td>
            </tr>
            <tr>
                <td>Pangkat/Golongan</td>
                <td>:</td>
                <td>-</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Kepala Sekolah</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $school_name ?? 'SMK PGRI TELAGASARI' }}</td>
            </tr>
        </table>

        <p style="text-align: center; font-weight: bold; margin: 25px 0;">MENUGASKAN</p>

        <table class="content-table" style="margin-left: 20px;">
            <tr>
                <td width="25%">Nama</td>
                <td width="3%">:</td>
                <td><strong>{{ $visit->pembimbing->teacher->name ?? $visit->pembimbing->name }}</strong></td>
            </tr>
            <tr>
                <td>NPA/NIP</td>
                <td>:</td>
                <td>{{ $visit->pembimbing->teacher->nip ?? '-' }}</td>
            </tr>
            <tr>
                <td>Jabatan</td>
                <td>:</td>
                <td>Guru Bidang Studi / Pembimbing</td>
            </tr>
            <tr>
                <td>Unit Kerja</td>
                <td>:</td>
                <td>{{ $school_name ?? 'SMK PGRI TELAGASARI' }}</td>
            </tr>
            <tr>
                <td>Untuk</td>
                <td>:</td>
                <td><strong>{{ $visit->purpose }}</strong></td>
            </tr>
            <tr>
                <td>Hari</td>
                <td>:</td>
                <td>{{ $hari ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>:</td>
                <td>{{ $tanggalBerangkat ?? '-' }}</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>08.00 WIB s.d Selesai</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td><strong>{{ $visit->industry->name }}</strong><br>{{ $visit->industry->address ?? '-' }}</td>
            </tr>
        </table>

        <p style="text-align: justify; margin-top: 25px; text-indent: 40px;">
            Demikian Surat Tugas ini dibuat agar yang bersangkutan bertugas dengan penuh rasa tanggungjawab dan
            menyampaikan laporan setelah selesai kegiatan.
        </p>

        <table class="ttd-box">
            <tr>
                <td width="55%"></td>
                <td width="45%" style="text-align: left;">
                    Karawang, {{ $tanggalSurat }}<br>
                    Kepala {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br><br><br><br><br>
                    <b><u>{{ !empty($kepsek_name) ? $kepsek_name : 'Kepala Sekolah ' . ($school_name ?? 'SMK PGRI TELAGASARI') }}</u></b><br>
                    NKRS/NIP. {{ !empty($kepsek_nip) ? $kepsek_nip : '-' }}
                </td>
            </tr>
        </table>

        <div class="page-break"></div>

        <div class="center-title">
            <span class="title-main">SURAT PERINTAH PERJALANAN DINAS (SPPD)</span><br>
            <span>Nomor: 30/SATDIK-SMK/II.03/G.{{ date('Y') }}</span>
        </div>

        <table class="bordered-table">
            <tr>
                <td width="5%" style="text-align: center;">1</td>
                <td width="45%">Pejabat berwenang yang memberi perintah</td>
                <td width="50%">Kepala Sekolah</td>
            </tr>
            <tr>
                <td style="text-align: center;">2</td>
                <td>Nama/NIP Pegawai yang diperintahkan</td>
                <td>{{ $visit->pembimbing->teacher->name ?? $visit->pembimbing->name }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">3</td>
                <td>a. Pangkat dan golongan<br>b. Jabatan/Instansi</td>
                <td><br>Guru Bidang Studi</td>
            </tr>
            <tr>
                <td style="text-align: center;">4</td>
                <td>Maksud perjalanan dinas</td>
                <td>{{ $visit->purpose }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">5</td>
                <td>Alat angkutan yang dipergunakan</td>
                <td>Kendaraan Umum / Pribadi</td>
            </tr>
            <tr>
                <td style="text-align: center;">6</td>
                <td>a. Tempat berangkat<br>b. Tempat Tujuan<br>c. Alamat</td>
                <td>{{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br><strong>{{ $visit->industry->name }}</strong><br>{{ $visit->industry->address ?? '-' }}
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">7</td>
                <td>a. Lamanya perjalanan<br>b. Tanggal berangkat<br>c. Tanggal harus kembali</td>
                <td>1 (Satu) Hari<br>{{ $tanggalBerangkat }}<br>{{ $tanggalBerangkat }}</td>
            </tr>
            <tr>
                <td style="text-align: center;">8</td>
                <td>Pengikut: Nama / Tgl Lahir / Keterangan</td>
                <td>-</td>
            </tr>
            <tr>
                <td style="text-align: center;">9</td>
                <td>Pembebanan anggaran:</td>
                <td>Instansi ({{ $school_name ?? 'SMK PGRI TELAGASARI' }})</td>
            </tr>
            <tr>
                <td style="text-align: center;">10</td>
                <td>Keterangan lain-lain</td>
                <td>Surat Perintah ini supaya dilaksanakan dengan rasa penuh tanggung jawab</td>
            </tr>
        </table>

        <table class="ttd-box">
            <tr>
                <td width="55%"></td>
                <td width="45%" style="text-align: left;">
                    Dikeluarkan di : Karawang<br>
                    Pada tanggal : {{ $tanggalSurat }}<br>
                    Kepala {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br><br><br><br><br>
                    <b><u>{{ !empty($kepsek_name) ? $kepsek_name : 'Kepala Sekolah ' . ($school_name ?? 'SMK PGRI TELAGASARI') }}</u></b><br>
                    NKRS/NIP. {{ !empty($kepsek_nip) ? $kepsek_nip : '-' }}
                </td>
            </tr>
        </table>

        <div class="page-break"></div>

        <table class="bordered-table" style="margin-top: 20px;">
            <tr>
                <td width="50%">
                    Berangkat dari : {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br>
                    (Tempat Kedudukan)<br>
                    Ke : {{ $visit->industry->name }}<br>
                    Pada Tanggal : {{ $tanggalBerangkat }}<br><br>
                    Kepala Sekolah,<br><br><br><br><br>
                    <b><u>{{ !empty($kepsek_name) ? $kepsek_name : 'Kepala Sekolah ' . ($school_name ?? 'SMK PGRI TELAGASARI') }}</u></b><br>
                    NKRS/NIP. {{ !empty($kepsek_nip) ? $kepsek_nip : '-' }}
                </td>
                <td width="50%">
                    I. Tiba di : {{ $visit->industry->name }}<br>
                    Pada Tanggal : {{ $tanggalBerangkat }}<br><br><br>
                    Kepala / Pimpinan Industri,<br><br><br><br><br>
                    <b>( _________________________ )</b>
                </td>
            </tr>
            <tr>
                <td>
                    II. Berangkat dari : {{ $visit->industry->name }}<br>
                    Ke : {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br>
                    Pada Tanggal : {{ $tanggalBerangkat }}<br><br><br>
                    Kepala / Pimpinan Industri,<br><br><br><br><br>
                    <b>( _________________________ )</b>
                </td>
                <td>
                    III. Tiba kembali di : {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br>
                    Pada Tanggal : {{ $tanggalBerangkat }}<br><br><br>
                    Kepala Sekolah,<br><br><br><br><br>
                    <b><u>{{ !empty($kepsek_name) ? $kepsek_name : 'Kepala Sekolah ' . ($school_name ?? 'SMK PGRI TELAGASARI') }}</u></b><br>
                    NKRS/NIP. {{ !empty($kepsek_nip) ? $kepsek_nip : '-' }}
                </td>
            </tr>
        </table>

        <p style="text-align: justify; margin-top: 15px;">
            Telah diperiksa dengan keterangan bahwa perjalanan tersebut atas perintahnya dan semata-mata untuk
            kepentingan jabatan dalam waktu yang sesingkat-singkatnya.
        </p>

        <table class="ttd-box" style="margin-top: 10px;">
            <tr>
                <td width="55%"></td>
                <td width="45%" style="text-align: left;">
                    Kepala {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br><br><br><br><br>
                    <b><u>{{ !empty($kepsek_name) ? $kepsek_name : 'Kepala Sekolah ' . ($school_name ?? 'SMK PGRI TELAGASARI') }}</u></b><br>
                    NKRS/NIP. {{ !empty($kepsek_nip) ? $kepsek_nip : '-' }}
                </td>
            </tr>
        </table>

        <div class="page-break"></div>

        <div class="center-title">
            <span class="title-main">LAPORAN PERJALANAN DINAS</span>
        </div>

        <p style="font-weight: bold; margin-bottom: 5px;">A. PENDAHULUAN</p>
        <table class="content-table" style="margin-left: 15px;">
            <tr>
                <td width="5%">1.</td>
                <td width="20%">Dasar</td>
                <td width="3%">:</td>
                <td>Surat Perintah Kepala {{ $school_name ?? 'SMK PGRI TELAGASARI' }}<br>Nomor:
                    30/SATDIK-SMK/II.03/G.{{ date('Y') }}<br>Tanggal: {{ $tanggalSurat }}</td>
            </tr>
            <tr>
                <td>2.</td>
                <td>Kegiatan</td>
                <td>:</td>
                <td>{{ $visit->purpose }}</td>
            </tr>
            <tr>
                <td>3.</td>
                <td>Waktu</td>
                <td>:</td>
                <td>{{ $tanggalBerangkat }}</td>
            </tr>
            <tr>
                <td>4.</td>
                <td>Lokasi</td>
                <td>:</td>
                <td>{{ $visit->industry->name }}</td>
            </tr>
        </table>

        <p style="font-weight: bold; margin-top: 30px; margin-bottom: 5px;">B. PENUTUP</p>
        <table class="content-table" style="margin-left: 15px;">
            <tr>
                <td width="25%">Kesimpulan</td>
                <td width="3%">:</td>
                <td class="dotted-line"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td class="dotted-line"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td class="dotted-line"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td class="dotted-line"></td>
            </tr>
        </table>

        <table class="ttd-box" style="margin-top: 40px;">
            <tr>
                <td width="55%"></td>
                <td width="45%" style="text-align: left;">
                    Karawang, ____________________ {{ date('Y') }}<br>
                    Yang Melaksanakan Tugas,<br><br><br><br><br>
                    <b><u>{{ $visit->pembimbing->teacher->name ?? $visit->pembimbing->name }}</u></b>
                </td>
            </tr>
        </table>

    </main>
</body>

</html>
