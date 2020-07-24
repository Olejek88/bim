<?php

namespace common\models;

use common\components\MainFunctions;
use Exception;
use Yii;

/**
 * This is the model class for table "register".
 * журнал содержит записи о всех событиях, которые связаны как с объектами, так и с каналами, которые с ними связаны
 * @property int $_id [int(11)]
 * @property string $uuid
 * @property string $title
 * @property integer $type
 * @property string $entityUuid
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */
class Register extends PoliterModel
{
    const TYPE_INFO = 0;
    const TYPE_WARNING = 1;
    const TYPE_ERROR = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'register';
    }

    /**
     * Check Register model.
     * @param $title
     * @param $type
     * @param $entityUuid
     * @return mixed
     * @throws Exception
     */

    public static function addRegister($title, $type, $entityUuid)
    {
        $model = new Register();
        $model->title = $title;
        $model->type = $type;
        $model->entityUuid = $entityUuid;
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
            [['uuid', 'type', 'title'], 'required'],
            [['uuid', 'entityUuid'], 'string', 'max' => 50],
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
            'type' => Yii::t('app', 'Тип'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['uuid', 'title', 'entityUuid', 'type', 'createdAt', 'changedAt'];
    }

    /**
     * Возвращает название связанной записи
     * @return string | null
     * @var Equipment $equipment
     */
    public function getEntityTitle()
    {
        if ($this->entityUuid != null) {
            $channel = MeasureChannel::findOne(['uuid' => $this->entityUuid]);
            if ($channel) {
                return $channel->object->getFullTitle() . '/' . $channel->title;
            }
            $object = Objects::findOne(['uuid' => $this->entityUuid]);
            if ($object) {
                return $object->getFullTitle();
            }
        }
        return '';
    }
}