<!DOCTYPE html>
<html>

<head>
    <title>Rapor PKL - {{ $name }}</title>
    <style>
        body {
            font-family: sans-serif;
            line-height: 1.6;
        }

        .kop {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .content {
            margin: 0 50px;
        }

        .title {
            text-align: center;
            font-weight: bold;
            font-size: 16pt;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            margin-bottom: 20px;
        }

        .score-box {
            border: 1px solid #000;
            padding: 10px;
            text-align: center;
            font-size: 20pt;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="kop">
        <h2>SMK PGRI TELAGASARI</h2>
        <p>Laporan Hasil Penilaian Praktik Kerja Lapangan</p>
    </div>
    <div class="content">
        <div class="title">SERTIFIKAT PENILAIAN</div>
        <table>
            <tr>
                <td width="30%">Nama</td>
                <td>: {{ $name }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>: {{ $nis }}</td>
            </tr>
            <tr>
                <td>Industri</td>
                <td>: {{ $industry }}</td>
            </tr>
            <tr>
                <td>Masa PKL</td>
                <td>: {{ $start_date }} s/d {{ $end_date }}</td>
            </tr>
        </table>
        <p>Berdasarkan hasil evaluasi, siswa yang bersangkutan dinyatakan telah menyelesaikan program PKL dengan nilai:
        </p>
        <div class="score-box">{{ $score }}</div>
        <p><strong>Catatan Pembimbing:</strong><br><em>"{{ $notes }}"</em></p>
        <div style="margin-top: 50px; float: right; text-align: center;">
            Karawang, {{ $date }} <br> Pembimbing, <br><br><br><br> <strong>{{ $evaluator }}</strong>
        </div>
    </div>
</body>

</html>
