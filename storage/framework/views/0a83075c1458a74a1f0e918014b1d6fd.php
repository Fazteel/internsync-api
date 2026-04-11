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

        .footer-table {
            width: 100%;
            margin-top: 50px;
            page-break-inside: avoid;
        }

        .footer-table td {
            vertical-align: bottom;
            text-align: center;
        }

        .title-text {
            text-align: center;
            font-weight: bold;
            margin: 20px 0;
            line-height: 1.5;
            font-size: 12pt;
        }

        .code-text {
            margin: 0;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 15%; text-align: left; padding-left: 5px;">
                <?php if(!empty($school_logo)): ?>
                    <img src="<?php echo e($school_logo); ?>" style="width: 115px; height: auto;">
                <?php endif; ?>
            </td>
            <td style="width: 70%;" class="header-text">
                <h3><?php echo e($yayasan_name); ?></h3>
                <h2><?php echo e(strtoupper($school_name)); ?></h2>
                <p>Jurusan : Teknik Pemeliharaan Mekanik Industri, Teknik Pemesinan</p>
                <p>Teknik Pengelasan, Teknik Kendaraan Ringan, Rekayasa Perangkat Lunak</p>
                <p><b>( TERAKREDITASI <?php echo e($accreditation); ?> )</b></p>
                <p><b>NPSN : <?php echo e($npsn); ?> NSS : <?php echo e($nss); ?></b></p>
                <p><?php echo e($school_address); ?></p>
                <p style="font-size: 8.5pt;">Tlp. <?php echo e($school_phone); ?> Email : <span
                        style="color: blue; text-decoration: underline;"><?php echo e($support_email); ?></span> Website : <span
                        style="color: blue; text-decoration: underline;"><?php echo e($school_website); ?></span></p>
            </td>
            <td style="width: 15%; text-align: center; padding-right: 5px; vertical-align: middle;">
                <img src="data:image/jpeg;base64,<?php echo e(base64_encode(@file_get_contents(public_path('images/iqs-image.jpg')))); ?>"
                    style="width: 45px; margin-bottom: 5px;"><br>
                <img src="data:image/png;base64,<?php echo e(base64_encode(@file_get_contents(public_path('images/kan-image.png')))); ?>"
                    style="width: 85px;">
            </td>
        </tr>
    </table>

    <p class="code-text">F.7.2.2.WBH. 9.Prakerin</p>

    <div class="title-text">
        <u>BERITA ACARA</u><br>
        SERAH TERIMA SISWA PRAKERIN
    </div>

    <p>Pada hari <b><?php echo e($hari_ini); ?></b> tanggal <b><?php echo e($tanggal_ini); ?></b>, bulan <b><?php echo e($bulan_ini); ?></b>,
        tahun <b><?php echo e($tahun_ini); ?></b>, bertempat di <b><?php echo e($industri); ?></b></p>
    <p>Telah dilaksanakan serah terima peserta PKL dari pihak sekolah ke pihak industri, yaitu :</p>

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
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                    <td><?php echo e($s->name); ?></td>
                    <td><?php echo e($s->nis); ?></td>
                    <td><?php echo e($s->kelas); ?></td>
                    <td><?php echo e($s->major->major_name ?? $s->jurusan); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <p>Demikian berita acara ini dibuat dengan sebenarnya.</p>

    <table class="footer-table">
        <tr>
            <td style="width: 50%;">
                <p style="margin: 0;">Pihak Industri,</p>
                <br><br><br><br>
                <p style="margin: 0;">______________________</p>
            </td>
            <td style="width: 50%;">
                <p style="margin: 0;">Karawang, <?php echo e($tanggal_surat); ?><br>Pihak Sekolah,</p>
                <?php if(!empty($qr_signature)): ?>
                    <img src="<?php echo e($qr_signature); ?>" alt="QR TTD" style="width: 90px; margin: 10px 0;">
                <?php else: ?>
                    <br><br><br><br>
                <?php endif; ?>
                <p style="margin: 0;"><b><u><?php echo e($wakasek_name); ?></u></b><br>NIP.<?php echo e($wakasek_nip); ?></p>
            </td>
        </tr>
    </table>
</body>

</html>
<?php /**PATH C:\Users\USER\Documents\TUGAS AKHIR\InternSync\internsync-api\resources\views/pdf/berita_acara.blade.php ENDPATH**/ ?>