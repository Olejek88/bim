<?php

namespace common\models;

use common\components\MainFunctions;
use Exception;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "service_register".
 *
 * @property int $_id [int(11)]
 * @property string $uuid
 * @property string $service
 * @property integer $type
 * @property integer $view
 * @property string $entityUuid
 * @property string $description
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */
class ServiceRegister extends ActiveRecord
{
    const TYPE_INFO = 0;
    const TYPE_WARNING = 1;
    const TYPE_ERROR = 2;

    const SERVICE_IMPORT = '[politer]';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_register';
    }

    /**
     * Check ServiceRegister model.
     * @param $service
     * @param $type
     * @param $entityUuid
     * @param $description
     * @return mixed
     * @throws Exception
     */

    public static function addServiceRegister($service, $type, $entityUuid, $description)
    {
        $model = new ServiceRegister();
        $model->service = $service;
        $model->type = $type;
        $model->view = 0;
        $model->entityUuid = $entityUuid;
        $model->description = $description;
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
            [['uuid', 'type', 'view', 'service', 'description'], 'required'],
            [['uuid', 'entityUuid'], 'string', 'max' => 50],
            [['description'], 'string'],
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
            'description' => Yii::t('app', 'Описание'),
            'service' => Yii::t('app', 'Сервис'),
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
        return ['uuid', 'service', 'description', 'type', 'createdAt', 'changedAt', 'view'];
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