<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table>
    <tr>
        <td colspan="8" style="font-weight: bold; text-align: center; font-size: 14px;">Lembar Monitoring Praktek Kerja
            Industri</td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold; text-align: center; font-size: 14px;">SMK PGRI TELAGASARI Tahun
            Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}</td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold; text-align: center; font-size: 12px;">(Periode
            {{ \Carbon\Carbon::parse($visit->planned_date)->translatedFormat('F Y') }})</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Pada</td>
        <td style="font-weight: bold;">:</td>
        <td colspan="6" style="font-weight: bold;">{{ $visit->industry->name }}</td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Periode Tanggal</td>
        <td style="font-weight: bold;">:</td>
        <td colspan="6" style="font-weight: bold;">
            {{ \Carbon\Carbon::parse($visit->planned_date)->translatedFormat('d F Y') }}</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>

    <tr>
        <td rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">No</td>
        <td rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">Nama Siswa
        </td>
        <td rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">L/P</td>
        <td rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">Kelas</td>
        <td rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">Hasil
            Monitoring</td>
        <td colspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">CAP &amp;
            Tanda Tangan</td>
        <td rowspan="2"
            style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">Tanggal
            Monitoring</td>
    </tr>
    <tr>
        <td style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">DU / DI
        </td>
        <td style="border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: middle;">Pembimbing
            Sekolah</td>
    </tr>

    @foreach ($students as $index => $student)
        <tr>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $index + 1 }}</td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $student['name'] }}</td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                {{ $student['gender'] ?? 'L/P' }}</td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">{{ $student['kelas'] }}
            </td>
            <td style="border: 1px solid #000000; vertical-align: middle;">{{ $student['notes'] }}</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                {{ \Carbon\Carbon::parse($visit->planned_date)->translatedFormat('d/m/Y') }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; font-weight: bold;">Mengetahui,</td>
        <td colspan="4"></td>
        <td colspan="2" style="text-align: center;">Karawang, ……………………………….. {{ date('Y') }}</td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; font-weight: bold;">Pimpinan DU/DI</td>
        <td colspan="4"></td>
        <td colspan="2" style="text-align: center; font-weight: bold;">Pembimbing Sekolah</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center;">(...........................................)</td>
        <td colspan="4"></td>
        <td colspan="2" style="text-align: center; font-weight: bold; text-decoration: underline;">
            {{ $visit->pembimbing->teacher->name ?? ($visit->pembimbing->name ?? '-') }}</td>
    </tr>
</table>
