<!DOCTYPE html>
<html>
<head>
    <title>Surat Pengantar PKL</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 12pt; line-height: 1.5; margin: 40px; }
        .header { text-align: center; border-bottom: 3px solid black; padding-bottom: 10px; margin-bottom: 20px; }
        .kop-instansi { font-size: 16pt; font-weight: bold; text-transform: uppercase; margin: 0; }
        .kop-alamat { font-size: 10pt; margin: 0; }
        .content { margin-top: 30px; text-align: justify; }
        .ttd { width: 300px; float: right; text-align: center; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="header">
        <p class="kop-instansi">SMK PGRI TELAGASARI</p>
        <p class="kop-alamat">Jl. Raya Telagasari - Kosambi, Karawang, Jawa Barat</p>
    </div>

    <div class="content">
        <p style="text-align: right;">Karawang, {{ $tanggalSurat }}</p>
        
        <p>Nomor: 421.5/001/HUBIN/{{ date('Y') }}<br>
           Perihal: <b>Surat Pengantar Praktik Kerja Lapangan (PKL)</b><br>
           Lampiran: -
        </p>

        <p>Kepada Yth.<br>
           <b>{{ $hrName }}<br>
           {{ $namaPerusahaan }}</b><br>
           {{ $alamatPerusahaan }}
        </p>

        <p>Dengan hormat,</p>
        <p>Sehubungan dengan pelaksanaan program Praktik Kerja Lapangan (PKL) tahun ajaran {{ date('Y') }}, kami bermaksud mengirimkan siswa/i kami untuk melaksanakan PKL di instansi/perusahaan yang Bapak/Ibu pimpin selama <b>{{ $durasi }} bulan</b>, terhitung mulai tanggal <b>{{ $tanggalMulai }}</b>.</p>

        <p>Adapun data siswa/i tersebut adalah sebagai berikut:</p>
        <table style="width: 100%; margin-left: 20px; margin-bottom: 20px;">
            <tr><td style="width: 150px;">Nama Lengkap</td><td>: <b>{{ $namaSiswa }}</b></td></tr>
            <tr><td>NIS</td><td>: {{ $nisSiswa }}</td></tr>
            <tr><td>Kompetensi Keahlian</td><td>: {{ $jurusanSiswa }}</td></tr>
        </table>

        <p>Demikian surat pengantar ini kami sampaikan. Atas kesediaan dan kerjasama Bapak/Ibu, kami ucapkan terima kasih.</p>
    </div>

    <div class="ttd">
        <p>Ketua Hubungan Industri,</p>
        <br><br><br><br>
        <p><b>_________________________</b></p>
    </div>
</body>
</html>