<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lembar Penilaian PKL - {{ $name }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12pt;
            color: #333;
            line-height: 1.5;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 10pt;
        }

        .title {
            text-align: center;
            text-decoration: underline;
            font-weight: bold;
            font-size: 14pt;
            margin-bottom: 30px;
        }

        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px 0;
            vertical-align: top;
        }

        .score-box {
            border: 2px solid #000;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            background-color: #f9f9f9;
        }

        .score-value {
            font-size: 40pt;
            font-weight: bold;
            display: block;
        }

        .grade-value {
            font-size: 16pt;
            font-weight: bold;
        }

        .notes-section {
            margin-top: 20px;
            min-height: 100px;
        }

        .notes-content {
            border: 1px solid #ccc;
            padding: 15px;
            font-style: italic;
            background-color: #fff;
        }

        .signature-table {
            width: 100%;
            margin-top: 50px;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>SMK PGRI TELAGASARI</h2>
            <p>Jl. Syekh Quro No.1, Telagasari, Karawang, Jawa Barat 41381</p>
            <p>Website: smkpgritelagasari.sch.id | Email: info@smkpgritelagasari.sch.id</p>
        </div>

        <div class="title">LEMBAR HASIL PENILAIAN PRAKTIK KERJA LAPANGAN (PKL)</div>

        <table class="info-table">
            <tr>
                <td width="25%">Nama Siswa</td>
                <td width="2%">:</td>
                <td width="73%"><strong>{{ $name }}</strong></td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $nis }}</td>
            </tr>
            <tr>
                <td>Industri Mitra</td>
                <td>:</td>
                <td>{{ $industry }}</td>
            </tr>
        </table>

        <div class="score-box">
            <span style="font-size: 10pt; text-transform: uppercase;">Skor Akhir</span>
            <span class="score-value">{{ $score }}</span>
            <span class="grade-value">Predikat: {{ $grade }}</span>
        </div>

        <div class="notes-section">
            <p><strong>Catatan Evaluasi / Feedback:</strong></p>
            <div class="notes-content">
                {{ $notes ?: 'Tidak ada catatan tambahan dari pembimbing.' }}
            </div>
        </div>

        <table class="signature-table">
            <tr>
                <td width="60%"></td>
                <td width="40%" text-align="center">
                    Karawang, {{ $date }}<br>
                    Guru Pembimbing,
                    <br><br><br><br><br>
                    <strong><u>{{ $evaluator }}</u></strong><br>
                    NIP/NUPTK. -
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Dokumen ini diterbitkan secara digital oleh Sistem Manajemen PKL InternSync - {{ date('Y') }}
    </div>
</body>

</html>
