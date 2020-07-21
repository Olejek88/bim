<?php

namespace frontend\assets;

use yii\web\AssetBundle;

class SelectAreaFeatureAsset extends AssetBundle
{
    public $depends = [
        'dosamigos\leaflet\LeafLetAsset',
    ];

    public $css = [
    ];

    public $js = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
//        $this->sourcePath = __DIR__ . '/assets';
        $this->js = [
            'js/Leaflet.SelectAreaFeature.js',
        ];
        $this->css = [
        ];
    }
}