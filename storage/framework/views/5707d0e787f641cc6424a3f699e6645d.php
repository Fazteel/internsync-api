<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table>
    <tr>
        <td colspan="8" style="font-weight: bold; text-align: center; font-size: 14px;">Lembar Monitoring Praktek Kerja
            Industri</td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold; text-align: center; font-size: 14px;">SMK PGRI TELAGASARI Tahun
            Pelajaran <?php echo e(date('Y')); ?>/<?php echo e(date('Y') + 1); ?></td>
    </tr>
    <tr>
        <td colspan="8" style="font-weight: bold; text-align: center; font-size: 12px;">(Periode
            <?php echo e(\Carbon\Carbon::parse($visit->planned_date)->translatedFormat('F Y')); ?>)</td>
    </tr>
    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Pada</td>
        <td style="font-weight: bold;">:</td>
        <td colspan="6" style="font-weight: bold;"><?php echo e($visit->industry->name); ?></td>
    </tr>
    <tr>
        <td style="font-weight: bold;">Periode Tanggal</td>
        <td style="font-weight: bold;">:</td>
        <td colspan="6" style="font-weight: bold;">
            <?php echo e(\Carbon\Carbon::parse($visit->planned_date)->translatedFormat('d F Y')); ?></td>
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

    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;"><?php echo e($index + 1); ?></td>
            <td style="border: 1px solid #000000; vertical-align: middle;"><?php echo e($student['name']); ?></td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                <?php echo e($student['gender'] ?? 'L/P'); ?></td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;"><?php echo e($student['kelas']); ?>

            </td>
            <td style="border: 1px solid #000000; vertical-align: middle;"><?php echo e($student['notes']); ?></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000; text-align: center; vertical-align: middle;">
                <?php echo e(\Carbon\Carbon::parse($visit->planned_date)->translatedFormat('d/m/Y')); ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <tr>
        <td colspan="8"></td>
    </tr>
    <tr>
        <td colspan="2" style="text-align: center; font-weight: bold;">Mengetahui,</td>
        <td colspan="4"></td>
        <td colspan="2" style="text-align: center;">Karawang, ……………………………….. <?php echo e(date('Y')); ?></td>
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
            <?php echo e($visit->pembimbing->teacher->name ?? ($visit->pembimbing->name ?? '-')); ?></td>
    </tr>
</table>
<?php /**PATH C:\Users\USER\Documents\TUGAS AKHIR\InternSync\internsync-api\resources\views/excel/monitoring.blade.php ENDPATH**/ ?>