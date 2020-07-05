<?php

use common\models\Register;
use common\models\Settings;
use yii\helpers\Html;
use yii\widgets\Pjax;

$registers = Register::find()->orderBy('createdAt DESC')->limit(7)->all();
$settings = Settings::find()->all();

foreach ($settings as $setting) {
//    if ($setting['uuid'] == Settings::SETTING_PERIOD)
//        $period = $setting['parameter'];
}
?>
<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li><a href="#control-sidebar-references-tab" data-toggle="tab"><i class="fa fa-book"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading"><?php echo Yii::t('app', 'Последние события') ?></h3>
            <ul class="control-sidebar-menu">
                <?php
                $count = 0;
                foreach ($registers as $register) {
                    print '<li><a href="javascript:void(0)">';
                    print '<i class="menu-icon fa fa-calendar bg-blue"></i>';
                    print '<div class="menu-info">
                                <h4 class="control-sidebar-subheading">' . $register['createdAt'] . '</h4>
                           <p>' . $register['title'] . '</p>
                           </div></a></li>';
                }
                ?>
            </ul>

        </div>
        <div class="tab-pane" id="control-sidebar-stats-tab"><?php echo Yii::t('app', 'Настройки') ?></div>
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <?php Pjax::begin(['id' => 'options']); ?>
            <?= Html::beginForm(['../site/config'], 'post', ['data-pjax' => '', 'class' => 'form-inline']); ?>
            <h4 class="control-sidebar-heading"><?php echo Yii::t('app', 'Основные настройки') ?></h4>
            <?= Html::hiddenInput('url', Yii::$app->request->getUrl(), ['id' => 'url',]); ?>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                </label>
            </div>
            <button type="submit"
                    class="btn btn-info btn-sm"><?php echo Yii::t('app', 'сохранить настройки') ?></button>
            <?php
            echo Html::endForm();
            Pjax::end();
            ?>
        </div>

        <div class="tab-pane" id="control-sidebar-stats-tab"><?php echo Yii::t('app', 'Настройки') ?></div>
        <div class="tab-pane" id="control-sidebar-references-tab">
            <h3 class="control-sidebar-heading"><?php echo Yii::t('app', 'Справочники') ?></h3>
            <div class="form-group">
                <a href="../../object-type" style="color: white"><i class="fa fa-building"></i>&nbsp;
                    <?= Yii::t('app', 'Типы объектов'); ?></a><br/>
                <a href="../../object-sub-type" style="color: white"><i class="fa fa-building"></i>&nbsp;
                    <?= Yii::t('app', 'Подтипы объектов'); ?></a><br/>
                <a href="../../attribute-type" style="color: white"><i class="fa fa-list"></i>&nbsp;
                    <?= Yii::t('app', 'Типы атрибутов'); ?></a><br/>
                <a href="../../parameter-type" style="color: white"><i class="fa fa-paste"></i>&nbsp;
                    <?= Yii::t('app', 'Типы параметров'); ?></a><br/>
                <a href="../../measure-type" style="color: white"><i class="fa fa-bar-chart"></i>&nbsp;
                    <?= Yii::t('app', 'Типы измерений'); ?></a><br/>
            </div>
            <br/>
        </div>
    </div>
</aside>
<div class="control-sidebar-bg"></div>
