<?php

/** @var string $message */

$this->title = Yii::t('app', 'Произошла ошибка');
?>


<div class="wrapper-block">
    <div style="padding-top: 100px;">
        <div class="panel panel-default" style="width: 600px; margin: 0 auto; padding: 10px;">
            <div class="panel-body">
                <section class="content">

                    <div class="error-page">
                        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>
                        <div class="error-content">
                            <br>
                            <?= Yii::t('app', 'Возникла ошибка.') ?><br/>
                            <?= Yii::t('app', 'Наши программисты изучат информацию') ?><br/>
                            <?= Yii::t('app', 'и устранят ее в кратчайшие сроки.') ?><br/>
                            <br/><?= Yii::t('app', 'Вернуться к ') . '<a href=' .
                            Yii::$app->homeUrl . '> ' . Yii::t('app', 'главной странице') . '</a>' ?>

                        </div>
                    </div>

                </section>

            </div>
        </div>
    </div>

</div>
