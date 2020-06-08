<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "object_type".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $createdAt
 * @property string $changedAt
 */
class ObjectType extends ActiveRecord
{
    const REGION = '4F7BC1D4-EA62-400F-9EC2-1BA289C7FCE2';
    const DISTRICT = '40876BDE-4933-443E-B3D1-5E8610DE8E24';
    const CITY = '9270E308-6125-42D3-93AD-A976E3DD5D2F';
    const CITY_DISTRICT = '461CDEBF-A320-4A96-BE6E-788B3E9267CF';
    const SUB_DISTRICT = '6A131086-AEAA-4513-8C9D-3CEAA979A2EC';
    const STREET = 'B23955CC-13AC-4D17-9394-5928C3D0A321';
    const OBJECT = 'A20871DA-A65D-42CB-983F-0B106C507F29';
    //2F2F9BED-4FAC-43D6-A16B-FD59BD870146
    //62333CB5-D064-44C5-8723-7B0983287613
    //0716C884-3762-42EF-BF91-CE7B21E9D21A

    /**
     * Название таблицы
     *
     * @inheritdoc
     *
     * @return string
     */
    public static function tableName()
    {
        return 'object_type';
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
                'value' => new Expression('NOW()'),
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
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
            [['uuid', 'title'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],
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
            'title' => Yii::t('app', 'Название'),
            'createdAt' => Yii::t('app', 'Создан'),
            'changedAt' => Yii::t('app', 'Изменен'),
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
}
