<?php

/* @var $model Objects */

/* @var $channels */

use common\models\Objects;

?>
<div id="<?= $model['_id'] ?>" class="kv-expand-row kv-grid-demo">
    <div class="kv-detail-content">
        <h3><?php echo $model->getFullTitle() ?>
        </h3>
        <div class="row">
        </div>
    </div>
</div>
