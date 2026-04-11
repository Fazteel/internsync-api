<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title); ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header h2,
        .header h3,
        .header p {
            margin: 0;
            padding: 2px 0;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .table-data th {
            background-color: #f4f4f4;
            font-weight: bold;
            text-align: center;
        }

        .text-center {
            text-align: center !important;
        }

        .footer {
            margin-top: 40px;
            width: 100%;
            page-break-inside: avoid;
        }

        .footer-table {
            width: 100%;
            border: none;
        }

        .footer-table td {
            border: none;
            padding: 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2><?php echo e(strtoupper($school_name)); ?></h2>
        <h3>LAPORAN LOGBOOK HARIAN PRAKERIN</h3>
        <p>Dicetak pada: <?php echo e($date); ?></p>
    </div>

    <table class="table-data">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 20%;">Nama Siswa</th>
                <th style="width: 20%;">Industri</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 40%;">Deskripsi Aktivitas</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $logbooks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center"><?php echo e($index + 1); ?></td>
                    <td>
                        <strong><?php echo e($log->internship->student->user->name ?? ($log->internship->student->name ?? '-')); ?></strong><br>
                        <span style="font-size: 10px; color: #666;">NIS:
                            <?php echo e($log->internship->student->nis ?? '-'); ?></span>
                    </td>
                    <td><?php echo e($log->internship->industry->name ?? '-'); ?></td>
                    <td class="text-center"><?php echo e(\Carbon\Carbon::parse($log->date)->translatedFormat('d M Y')); ?></td>
                    <td><?php echo e($log->activity); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data logbook.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <table class="footer-table">
            <tr>
                <td style="width: 60%;"></td>
                <td style="width: 40%; text-align: center;">
                    <p>Mengetahui,</p>
                    <p>Pembimbing Prakerin</p>

                    <?php if(!empty($qr_signature)): ?>
                        <img src="<?php echo e($qr_signature); ?>" alt="QR TTD" style="width: 90px; margin: 10px 0;">
                    <?php else: ?>
                        <br><br><br><br>
                    <?php endif; ?>

                    <p><b><u><?php echo e($pembimbing_name); ?></u></b></p>
                    <p>NIP. <?php echo e($pembimbing_nip); ?></p>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>
<?php /**PATH C:\Users\USER\Documents\TUGAS AKHIR\InternSync\internsync-api\resources\views/pdf/logbook_report.blade.php ENDPATH**/ ?>