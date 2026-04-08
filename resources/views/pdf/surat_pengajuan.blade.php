<!DOCTYPE html>
<html>

<head>
    <style>
        @page {
            margin: 30px 40px;
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
            margin-top: 4px;
            margin-bottom: 15px;
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
            font-size: 18pt;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .header-text p {
            margin: 0;
            font-size: 9.5pt;
        }

        .content-table {
            width: 100%;
            margin-bottom: 15px;
        }

        .content-table td {
            vertical-align: top;
        }

        .student-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }

        .student-table th,
        .student-table td {
            border: 1px solid black;
            padding: 5px;
            text-align: left;
            font-size: 10pt;
        }

        /* LAYOUT FOOTER & TTD */
        .footer-table {
            width: 100%;
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .footer-table td {
            vertical-align: bottom;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left; padding-left: 5px;">
                @if (!empty($school_logo))
                    <img src="{{ $school_logo }}" style="width: 115px; height: auto;">
                @endif
            </td>
            <td style="width: 70%;" class="header-text">
                <h3>{{ $yayasan_name }}</h3>
                <h2>{{ strtoupper($school_name) }}</h2>
                <p>Jurusan : Teknik Pemeliharaan Mekanik Industri, Teknik Pemesinan</p>
                <p>Teknik Pengelasan, Teknik Kendaraan Ringan, Rekayasa Perangkat Lunak</p>
                <p><b>( TERAKREDITASI {{ $accreditation }} )</b></p>
                <p><b>NPSN : {{ $npsn }} NSS : {{ $nss }}</b></p>
                <p>{{ $school_address }}</p>
                <p style="font-size: 8.5pt;">Tlp. {{ $school_phone }} Email : <span
                        style="color: blue; text-decoration: underline;">{{ $support_email }}</span> Website : <span
                        style="color: blue; text-decoration: underline;">{{ $school_website }}</span></p>
            </td>
            <td style="width: 15%; text-align: center; padding-right: 5px; vertical-align: middle;">
                <img src="data:image/jpeg;base64,{{ base64_encode(@file_get_contents(public_path('images/iqs-image.jpg'))) }}"
                    style="width: 45px; margin-bottom: 5px;"><br>
                <img src="data:image/png;base64,{{ base64_encode(@file_get_contents(public_path('images/kan-image.png'))) }}"
                    style="width: 85px;">
            </td>
        </tr>
    </table>

    <table class="content-table">
        <tr>
            <td width="15%">Nomor</td>
            <td>: {{ $nomor_surat }}</td>
            <td align="right">{{ $tanggal_surat }}</td>
        </tr>
        <tr>
            <td>Lampiran</td>
            <td>: - </td>
            <td></td>
        </tr>
        <tr>
            <td>Perihal</td>
            <td>: <b>{{ $perihal }}</b></td>
            <td></td>
        </tr>
    </table>

    <p>Kepada, Yth.<br><b>HRD Manager {{ $industri }}</b><br>di {{ $alamat_industri }}</p>

    <p>Dengan Hormat,</p>
    <p>
        Dalam upaya meningkatkan siswa di bidang pengetahuan dan teknologi, salah satu program pendidikan
        SMK PGRI Telagasari Karawang adalah melaksanakan prakerin program Pendidikan Sistem Ganda (PSG)
        bagi siswa <b>kelas {{ $grade_name }} (Semester {{ $semester_count }})</b>.
    </p>

    <p>Kami mohon bantuan Bapak/Ibu untuk memberikan kesempatan praktek kepada siswa kami berikut:</p>

    <table class="student-table">
        <thead>
            <tr>
                <th style="text-align: center; width: 5%;">No</th>
                <th style="width: 30%;">Nama Siswa</th>
                <th style="width: 20%;">NIS</th>
                <th style="width: 15%;">Kelas</th>
                <th style="width: 30%;">Program Keahlian</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $index => $s)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $s->name }}</td>
                    <td>{{ $s->nis }}</td>
                    <td>{{ $s->kelas }}</td>
                    <td>{{ $s->major->major_name ?? $s->jurusan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Waktu pelaksanaan prakerin direncanakan mulai tanggal <b>{{ $start_date }} s.d {{ $end_date }}</b>.</p>
    <p>Demikian surat permohonan ini, atas kerjasamanya kami ucapkan terima kasih.</p>

    <table class="footer-table">
        <tr>
            <td style="width: 50%; text-align: left;">
                <p style="font-size: 10pt; margin: 0;">
                    Contact Person:<br>
                    {{ $pembimbing }} ({{ $phone_pembimbing }})
                </p>
            </td>

            <td style="width: 50%; text-align: center;">
                <p style="margin: 0;">Karawang, {{ $tanggal_surat }}<br>Wakasekbid Hubin,</p>

                @if (!empty($qr_signature))
                    <img src="{{ $qr_signature }}" alt="QR TTD" style="width: 90px; margin: 10px 0;">
                @else
                    <br><br><br><br>
                @endif

                <p style="margin: 0;"><b><u>{{ $wakasek_name }}</u></b></p>
            </td>
        </tr>
    </table>

</body>

</html>
