<!DOCTYPE html>
<html>
<head>
    <title>SPPD - {{ $visit->pembimbing->name }}</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; }
        .header { text-align: center; border-bottom: 3px double #000; pb: 10px; mb: 20px; }
        .title { text-align: center; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        td { vertical-align: top; padding: 5px; }
        .footer { margin-top: 50px; float: right; width: 250px; text-align: center; }
        .space { height: 70px; }
    </style>
</head>
<body>
    <div class="header">
        <h3 style="margin:0">YAYASAN PENDIDIKAN PGRI TELAGASARI</h3>
        <h2 style="margin:0">SMK PGRI TELAGASARI</h2>
        <p style="margin:0; font-size: 10pt;">Jl. Syekh Quro No. 5, Telagasari, Karawang, Jawa Barat</p>
    </div>

    <div class="title">SURAT PERINTAH PERJALANAN DINAS (SPPD)</div>

    <table>
        <tr>
            <td width="30%">Nama Guru/Pembimbing</td>
            <td width="5%">:</td>
            <td><strong>{{ $visit->pembimbing->name }}</strong></td>
        </tr>
        <tr>
            <td>NIP/NUPTK</td>
            <td>:</td>
            <td>{{ $visit->pembimbing->nip ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tujuan Industri</td>
            <td>:</td>
            <td>{{ $visit->industry->name }}</td>
        </tr>
        <tr>
            <td>Alamat Industri</td>
            <td>:</td>
            <td>{{ $visit->industry->address ?? '-' }}</td>
        </tr>
        <tr>
            <td>Tanggal Kunjungan</td>
            <td>:</td>
            <td>{{ \Carbon\Carbon::parse($visit->planned_date)->translatedFormat('l, d F Y') }}</td>
        </tr>
        <tr>
            <td>Maksud Perjalanan</td>
            <td>:</td>
            <td>{{ $visit->purpose }}</td>
        </tr>
    </table>

    <div style="margin-top: 30px;">
        <p>Demikian surat tugas ini diberikan agar dapat dipergunakan sebagaimana mestinya dan dilaksanakan dengan penuh tanggung jawab.</p>
    </div>

    <div class="footer">
        <p>Telagasari, {{ date('d F Y') }}</p>
        <p>Kepala Hubungan Industri,</p>
        <div class="space"></div>
        <p><strong>( ________________________ )</strong></p>
    </div>
</body>
</html>