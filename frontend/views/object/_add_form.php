<?php
/* @var $object Objects */
/* @var $objects Objects [] */
/* @var $object_uuid */
/* @var $objectTypes */

/* @var $objectSubTypes */

use common\components\MainFunctions;
use common\models\Objects;
use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\plugins\geocoder\GeoCoder;
use dosamigos\leaflet\plugins\geocoder\ServiceNominatim;
use dosamigos\leaflet\types\Icon;
use dosamigos\leaflet\types\LatLng;
use dosamigos\leaflet\widgets\Map;
use kartik\select2\Select2;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

?>

<?php $form = ActiveForm::begin([
    'enableAjaxValidation' => false,
    'options' => [
        'id' => 'form-object',
        'enctype' => 'multipart/form-data'
    ]]);
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4 class="modal-title"><?php echo Yii::t('app', 'Добавить объект') ?></h4>
</div>
<div class="modal-body">
    <?php
    $latDefault = 55.160374;
    $lngDefault = 61.402738;
    if ($object['uuid']) {
        echo Html::hiddenInput("objectUuid", $object['uuid']);
        echo $form->field($object, 'uuid')
            ->hiddenInput(['value' => $object['uuid']])
            ->label(false);
    } else {
        echo $form->field($object, 'uuid')
            ->hiddenInput(['value' => MainFunctions::GUID()])
            ->label(false);
    }


    echo $form->field($object, 'title')->textInput(['maxlength' => true]);
    echo $form->field($object, 'deleted')->hiddenInput(['value' => 0])->label(false);

    if (isset($object_uuid) && $object_uuid) {
        echo $form->field($object, 'parentUuid')->hiddenInput(['value' => $object_uuid])->label(false);
    } else {
        echo $form->field($object, 'parentUuid')->widget(Select2::class,
            [
                'data' => $objects,
                'language' => Yii::t('app', 'ru'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Не заполнять, если регион..')
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
    }

    echo $form->field($object, 'objectTypeUuid')->widget(Select2::class,
        [
            'data' => $objectTypes,
            'language' => Yii::t('app', 'ru'),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите тип..')
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($object, 'objectSubTypeUuid')->widget(Select2::class,
        [
            'data' => $objectSubTypes,
            'language' => Yii::t('app', 'ru'),
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите подтип..')
            ],
            'pluginOptions' => [
                'allowClear' => true
            ],
        ]);

    echo $form->field($object, 'fiasGuid')->textInput(['maxlength' => true]);
    echo $form->field($object, 'fiasParentGuid')->textInput(['maxlength' => true]);
    echo $form->field($object, 'okato')->textInput(['maxlength' => true]);

    $latitude = $object->latitude;
    $longitude = $object->longitude;

    if ($latitude && $longitude) {
        echo $form->field($object, 'latitude')->textInput(['maxlength' => true, 'value' => $latitude])->label(true);
        echo $form->field($object, 'longitude')->textInput(['maxlength' => true, 'value' => $longitude])->label(true);
    } else {
        echo $form->field($object, 'latitude')->textInput(['maxlength' => true, 'value' => $latDefault])->label(true);
        echo $form->field($object, 'longitude')->textInput(['maxlength' => true, 'value' => $lngDefault])->label(true);
    }

    // lets use nominating service
    $nominatim = new ServiceNominatim();

    // create geocoder plugin and attach the service
    $geoCoderPlugin = new GeoCoder([
        'service' => $nominatim,
        'clientOptions' => [
            // we could leave it to allocate a marker automatically
            // but I want to have some fun
            'showMarker' => false,
        ]
    ]);

    // first lets setup the center of our map
    if ($latitude && $longitude)
        $center = new LatLng(['lat' => $latitude, 'lng' => $longitude]);
    else
        $center = new LatLng(['lat' => $latDefault, 'lng' => $lngDefault]);

    // now lets create a marker that we are going to place on our map
    $icon = new Icon(['iconUrl' => '/images/marker-icon.png', 'shadowUrl' => '/images/marker-shadow.png']);
    $marker = new Marker([
        'latLng' => $center,
        'icon' => $icon,
//        'popupContent' => 'Hi!',
        'name' => 'geoMarker',
        'clientOptions' => [
            'draggable' => true,
            'icon' => $icon,
        ],
        'clientEvents' => [
            'dragend' => 'function(e){
                $("#objects-latitude").val(e.target._latlng.lat);
                $("#objects-longitude").val(e.target._latlng.lng);
            }'
        ],
    ]);
    // The Tile Layer (very important)
    $tileLayer = new TileLayer([
//        'urlTemplate' => 'https://a.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'urlTemplate' => 'https://{s}.tiles.mapbox.com/v4/mapquest.streets-mb/{z}/{x}/{y}.{ext}?access_token=pk.eyJ1IjoibWFwcXVlc3QiLCJhIjoiY2Q2N2RlMmNhY2NiZTRkMzlmZjJmZDk0NWU0ZGJlNTMifQ.mPRiEubbajc6a5y9ISgydg',
        'clientOptions' => [
            'attribution' => 'Tiles &copy; <a href="https://www.osm.org/copyright" target="_blank">OpenStreetMap contributors</a> />',
            'subdomains' => '1234',
//            'id' => 'mapbox.streets',
            'type' => 'osm',
            's' => 'a',
            'ext' => 'png',

        ]
    ]);

    // now our component and we are going to configure it
    $leafLet = new LeafLet([
        'name' => 'geoMap',
        'center' => $center,
        'tileLayer' => $tileLayer,
        'clientEvents' => [
            'geocoder_showresult' => 'function(e){
                // set markers position
                geoMarker.setLatLng(e.Result.center);
                $("#objects-latitude").val(e.Result.center.lat);
                $("#objects-longitude").val(e.Result.center.lng);
            }'
        ],
    ]);
    // Different layers can be added to our map using the `addLayer` function.
    $leafLet->addLayer($marker);      // add the marker
    //    $leafLet->addLayer($tileLayer);  // add the tile layer

    // install the plugin
    $leafLet->installPlugin($geoCoderPlugin);

    // finally render the widget
    try {
        echo Map::widget(['leafLet' => $leafLet]);
    } catch (Exception $exception) {
        echo '<div id="map"/>';
    }
    ?>

</div>
<div class="modal-footer">
    <?php echo Html::submitButton(Yii::t('app', 'Отправить'), ['class' => 'btn btn-success']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo Yii::t('app', 'Закрыть') ?></button>
</div>
<script>
    var send = false;
    $(document).on("beforeSubmit", "#form-object", function (e) {
        e.preventDefault();
    }).on('submit', '#form-object', function (e) {
        e.preventDefault();
        if (!send) {
            send = true;
            $.ajax({
                type: "post",
                data: $('#form-object').serialize(),
                url: "../object/save",
                success: function () {
                    $('#modalAdd').modal('hide');
                },
                error: function () {
                }
            });
        }
    });
</script>
<?php ActiveForm::end(); ?>
