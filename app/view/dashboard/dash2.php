<?php $DataReport = $this->model()->getThisQuery('SELECT * FROM (SELECT COUNT(peserta.id) AS peserta FROM peserta) AS ps, (SELECT COUNT(id) AS sekolah FROM sekolah) AS sk');
$DataCount = $DataReport[0]; ?>
<div class="row">
    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Peserta Daftar Per-Hari</h3>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">Tanggal</th>
                            <th class="text-right pr-5">Jumlah Peserta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $Recaps = $this->model()->getThisQuery('SELECT dates, COUNT(dates) AS jumlah FROM (SELECT SUBSTRING(id,1,6) AS dates FROM peserta) AS dt GROUP BY dates ORDER BY dates DESC LIMIT 6');
                        foreach ($Recaps as $rec) : ?>
                            <tr>
                                <td class="text-center"><?= $this->helper('Date')->toDate($rec['dates'], 20); ?></td>
                                <td class="text-right pr-5"><span class="text-success text-bold"><?= $rec['jumlah']; ?></span> orang</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-center">
                <a href="<?= BaseURL('data/participant'); ?>" class="text-sm">Lihat Semua Data Peserta<i class="fas fa-arrow-right ml-2"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="row">
            <div class="col-12">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3><?= number_format($DataCount['peserta'], 0, ",", "."); ?></h3>
                        <p>Data Peserta</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <a href="<?= BaseURL('data/participant'); ?>" class="small-box-footer">
                        Lihat Data<i class="fas fa-arrow-circle-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="col-12">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3><?= number_format($DataCount['sekolah'], 0, ",", "."); ?></h3>
                        <p>Sekolah Terdaftar</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-school"></i>
                    </div>
                    <a href="<?= BaseURL('school'); ?>" class="small-box-footer">
                        Lihat Data<i class="fas fa-arrow-circle-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>