<!DOCTYPE html>
<html>

<head>
    <title>Master Rekapitulasi PKL</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            margin-top: 30px;
            font-size: 8pt;
            color: #666;
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>LAPORAN MASTER REKAPITULASI PKL</h2>
        <p>SMK PGRI TELAGASARI - Tahun Pelajaran {{ date('Y') }}/{{ date('Y') + 1 }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Nama Industri</th>
                <th>Jurusan</th>
                <th width="10%">Total</th>
                <th width="10%">Selesai</th>
                <th width="15%">Rerata Nilai</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($distribution as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $row->industry_name }}</td>
                    <td>{{ $row->major_name }}</td>
                    <td class="text-center">{{ $row->total_students }}</td>
                    <td class="text-center">{{ $row->completed_count }}</td>
                    <td class="text-center">{{ $row->avg_score ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Dicetak pada: {{ date('d/m/Y H:i') }}</div>
</body>

</html>
