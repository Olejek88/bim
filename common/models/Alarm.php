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
 * @property int $level
 * @property string $entityUuid
 * @property string $createdAt [datetime]
 * @property string $changedAt [datetime]
 *
 */
class Alarm extends PoliterModel
{
    const LEVEL_FIXED = 0;

    const LEVEL_INFO = 1;
    const LEVEL_WARNING = 2;
    const LEVEL_PROBLEM = 3;
    const LEVEL_ERROR = 4;
    const LEVEL_CRITICAL = 5;

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
     * @param $level
     * @return mixed
     * @throws Exception
     */

    public static function addAlarm($entityUuid, $title, $level)
    {
        $model = new Alarm();
        $model->entityUuid = $entityUuid;
        $model->title = $title;
        $model->level = $level;
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
            [['level'], 'integer'],
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
            'level' => Yii::t('app', 'Уровень предупреждения'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * @return array|false
     */
    public function fields()
    {
        return ['uuid', 'title', 'entityUuid', 'level', 'createdAt', 'changedAt', 'view'];
    }

    /**
     * Возвращает название связанной записи
     * @return string | null
     * @var Equipment $equipment
     */
    public function getEntityTitle()
    {
        if ($this->entityUuid != null) {
            $object = Objects::findOne(['uuid' => $this->entityUuid]);
            if ($object) {
                return 'ул.' . $object->parent->title . ', ' . $this->title;
            }
        }
        return '';
    }

    /**
     * @return array
     */
    public function getPermissions()
    {
        $class = explode('\\', get_class($this));
        $class = $class[count($class) - 1];

        $perm = parent::getPermissions();
        $perm['list'] = 'list' . $class;
        $perm['validation'] = 'validation' . $class;

        return $perm;
    }

    /**
     * @return string
     */
    public function getAlarmLabel()
    {
        if ($this['level'] == Alarm::LEVEL_INFO)
            return '<span class="label label-info">Информация</span>';
        if ($this['level'] == Alarm::LEVEL_WARNING)
            return '<span class="label label-info">Предупреждение</span>';
        if ($this['level'] == Alarm::LEVEL_PROBLEM)
            return '<span class="label label-warning">Проблема</span>';
        if ($this['level'] == Alarm::LEVEL_ERROR)
            return '<span class="label label-warning">Тревога</span>';
        return '<span class="label label-danger">Критично</span>';
    }
}