<?php
/* @var $stats
 */
?>

<div class="row">
    <div class="col-md-12">
        <div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-cogs"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Каналов</span>
                <span class="info-box-number"><?= $stats['channels'] ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 20%"></div>
                </div>
                <span class="progress-description">
                    По <?= ' ' . $stats['channel_types'] . ' типам' ?>
                  </span>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="info-box bg-blue">
            <span class="info-box-icon"><i class="fa fa-database"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Данных</span>
                <span class="info-box-number"><?= $stats['data'] ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 20%"></div>
                </div>
                <span class="progress-description">
                    По <?= ' ' . $stats['data_types'] . ' типам' ?>
                  </span>
            </div>
        </div>
    </div>
</div>
