<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Expression;

/**
 * This is the model class for table "parameter".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $entityUuid
 * @property string $parameterTypeUuid
 * @property double $value
 * @property string $date
 * @property string $createdAt
 * @property string $changedAt
 *
 * @property ParameterType $parameterType
 */
class Parameter extends PoliterModel
{
    const DEFAULT_DATE = '2000-01-01 00:00:00';

    /**
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'parameter';
    }

    /**
     * Behaviors
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createdAt',
                'updatedAtAttribute' => 'changedAt',
                'value' => new Expression('NOW()')
            ],
        ];
    }

    /**
     * Rules
     *
     * @inheritdoc
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'parameterTypeUuid',
                    'value',
                    'date'
                ],
                'required'
            ],
            [['value'], 'number'],
            [['uuid', 'parameterTypeUuid', 'date', 'entityUuid'], 'string', 'max' => 50],
            [['createdAt', 'changedAt'], 'safe']
        ];
    }

    /**
     * Названия отрибутов
     *
     * @inheritdoc
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'parameterType' => Yii::t('app', 'Тип параметра'),
            'parameterTypeUuid' => Yii::t('app', 'Тип параметра'),
            'value' => Yii::t('app', 'Значение'),
            'date' => Yii::t('app', 'Дата'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid',
            'parameterTypeUuid',
            'parameterType' => function ($model) {
                return $model->parameterType;
            },
            'value',
            'date',
            'createdAt',
            'changedAt',
        ];
    }

    /**
     * Проверка целостности модели?
     *
     * @return bool
     */
    public function upload()
    {
        if ($this->validate()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Объект связанного поля.
     *
     * @return ActiveQuery
     */
    public function getParameterType()
    {
        return $this->hasOne(ParameterType::class, ['uuid' => 'parameterTypeUuid']);
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
     * Объект связанного поля.
     *
     * @return string
     */
    public function getEntityTitle()
    {
        if ($this->entityUuid) {
            /** @var Objects $object */
            $object = Objects::find()->where(['uuid' => $this->entityUuid])->one();
            if ($object) {
                return $object->getFullTitle();
            }
            /** @var MeasureChannel $channel */
            $channel = MeasureChannel::find()->where(['uuid' => $this->entityUuid])->one();
            if ($channel) {
                return $channel->title;
            }
        }
        return "";
    }
}
