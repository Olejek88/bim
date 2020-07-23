<?php
/* @var $marker
 * @var $coordinates
 */

use dosamigos\leaflet\controls\Layers;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\widgets\Map;

$this->registerJs('$(window).on("resize", function () { $("#w1").height(400);}).trigger("resize");');
?>
<div class="box box-success" style="height: 400px;">
    <div class="box-header with-border">
        <h3 class="box-title"><?php echo Yii::t('app', 'Расположение') ?></h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
            </button>
            <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i>
            </button>
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body no-padding">
        <div class="row">
            <div class="col-md-9 col-sm-8" style="width: 100%">
                <div class="pad" style="padding: 1px">
                    <?php
                    $center = $coordinates;
                    // The Tile Layer (very important)
                    $tileLayer = new TileLayer([
                        'urlTemplate' => 'https://api.tiles.mapbox.com/v4/mapbox.streets/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw',
                        'clientOptions' => [
                            'subdomains' => ['1', '2', '3', '4'],
                        ],
                    ]);
                    $leaflet = new LeafLet([
                        'center' => $center, // set the center
                        'zoom' => 17
                    ]);

                    $layers = new Layers();

                    // Different layers can be added to our map using the `addLayer` function.
                    $leaflet->addLayer($tileLayer);
                    $leaflet->addLayer($marker);

                    $layers->setOverlays([]);
                    $layers->setName('ctrlLayer');


                    echo Map::widget(['leafLet' => $leaflet]);
                    ?>
                </div>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
</div>
