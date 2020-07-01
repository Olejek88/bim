<?php

namespace common\models;

use common\components\MainFunctions;
use Exception;
use Yii;

/**
 * This is the model class for table "alarm".
 *
 * @property int $_id [int(11)]
 * @property string $uuid
 * @property string $title
 * @property string $entityUuid
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */
class Alarm extends PoliterModel
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'alarm';
    }

    /**
     * Check Alarm model.
     * @param $entityUuid
     * @param $title
     * @return mixed
     * @throws Exception
     */

    public static function addAlarm($entityUuid, $title)
    {
        $model = new Alarm();
        $model->entityUuid = $entityUuid;
        $model->title = $title;
        $model->uuid = MainFunctions::GUID();
        $model->save();
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title', 'entityUuid'], 'required'],
            [['uuid', 'entityUuid'], 'string', 'max' => 50],
            [['title'], 'string'],
            [['uuid', 'entityUuid'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],
        ];
    }

    /**
     * Labels.
     *
     * @return array
     *
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'uuid' => Yii::t('app', 'Uuid'),
            'entityUuid' => Yii::t('app', 'Связанная сущность'),
            'title' => Yii::t('app', 'Событие'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['uuid', 'title', 'entityUuid', 'createdAt', 'changedAt', 'view'];
    }

    /**
     * Возвращает название связанной записи
     * @return string | null
     * @var Equipment $equipment
     */
    public function getEntityTitle()
    {
        if ($this->entityUuid != null) {
            $equipment = Equipment::findOne(['uuid' => $this->entityUuid]);
            if ($equipment)
                return '[' . $equipment->_id . '] ' . $equipment->title;
        }
        return '';
    }
}