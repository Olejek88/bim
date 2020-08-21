<?php
/* @var $objects
 * @var $measure_types
 * @var $parameter_types
 */

use yii\helpers\Html;

$this->title = Yii::t('app', 'ПолиТЭР::Пользовательский отчет');

?>
<div class="row">
    <form action="/object/report" method="post" id="form" value="<?= Yii::$app->request->getCsrfToken() ?>">
        <div class="col-md-4">
            <?php
            echo Html::submitButton('Сформировать') . '<hr>';
            foreach ($objects as $object) {
                echo Html::checkbox('object' . $object['_id'], false, ['id' => $object['_id'], 'label' => $object->getFullTitle()]) . '</br>';
            }
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo 'Параметры объектов<hr>';
            foreach ($parameter_types as $type) {
                echo Html::checkbox('parameter' . $type['_id'], false, ['id' => $type['_id'], 'label' => $type['title']]) . '</br>';
            }
            ?>
        </div>
        <div class="col-md-4">
            <?php
            echo 'Типы измерений<hr>';
            echo Html::hiddenInput('forms', 1, ['id' => 'form']) . '</br>';
            echo Html::hiddenInput(Yii::$app->request->csrfParam, Yii::$app->request->getCsrfToken());
            foreach ($measure_types as $type) {
                echo Html::checkbox('measure' . $type['_id'], false, ['id' => $type['_id'], 'label' => $type['title']]) . '</br>';
            }
            ?>
        </div>
    </form>
</div>
