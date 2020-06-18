<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "settings".
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property string $parameter
 * @property string $createdAt
 * @property string $changedAt
 */
class Settings extends ActiveRecord
{
    /**
     * Table name.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @param $uuid String
     * @return int|mixed
     */
    public static function getSettings($uuid)
    {
        $parameter = 0;
        /** @var Settings $settings */
        $settings = Settings::find()
            ->where(['uuid' => $uuid])
            ->one();
        if ($settings) {
            return $settings['parameter'];
        } else {
            /*         if ($uuid == Settings::SETTING_MOVE_TEMPLATE)
                         $parameter = "0";
            */
            self::storeSetting($uuid, $parameter);
            return $parameter; // Возвращает параметр после сохранения.
        }

    }

    /**
     * @param $uuid
     * @param $parameter
     */
    public static function storeSetting($uuid, $parameter)
    {
        $settings = Settings::find()
            ->where(['uuid' => $uuid])
            ->one();
        if ($settings) {
            $settings['parameter'] = $parameter;
            $settings->save();
        } else {
            $settings = new Settings();
            $settings->title = 'Нет описания параметра!';
            /*           if ($uuid == Settings::SETTING_ULYANOVSK)
                           $settings->title = 'Применяются параметры Ульяновска';
            */
            $settings->uuid = $uuid;
            $settings->parameter = $parameter;
            $settings->save();
        }

    }

    /**
     * Behaviors.
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
     * Свойства объекта со связанными данными.
     *
     * @return array
     */
    public function fields()
    {
        return ['_id', 'uuid', 'title', 'parameter', 'createdAt', 'changedAt'];
    }

    /**
     * Rules.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'uuid',
                    'title',
                    'parameter',
                ],
                'required'
            ],
            [['createdAt', 'changedAt'], 'safe'],
            [
                [
                    'uuid',
                    'title',
                ],
                'string', 'max' => 50
            ],
            [['parameter'], 'string'],
            [['uuid', 'title'], 'filter', 'filter' => function ($param) {
                return htmlspecialchars($param, ENT_QUOTES | ENT_HTML401);
            }
            ],

        ];
    }

    /**
     * Метки для свойств.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_id' => Yii::t('app', '№'),
            'uuid' => Yii::t('app', 'Uuid'),
            'title' => Yii::t('app', 'Название'),
            'parameter' => Yii::t('app', 'Парметр'),
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
