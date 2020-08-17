<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "parameter_type"
 *
 * @property integer $_id
 * @property string $uuid
 * @property string $title
 * @property integer $type
 * @property string $createdAt
 * @property string $changedAt
 */
class ParameterType extends PoliterModel
{
    const TARGET_CONSUMPTION = '9716C884-3762-42EF-BF91-CE7B21E9D21A';
    const ENERGY_EFFICIENCY = '444CBA6D-2082-4DD7-9CC2-6EB5EBF1C5E0';
    const POWER_EQUIPMENT = '3EE2B734-B957-4191-82A7-60119C2C8556';

    const BASE_CONSUMPTION = 'D803C6CC-D567-4C37-85B7-8DBC0E43A272';
    const CONSUMPTION_COEFFICIENT = '10C48145-4EB3-43A9-9F5F-6E0CACB67CBD';

    const SQUARE = '2707720F-07DB-44BA-B25E-6DC3E9F40E7E';
    const VOLUME = '1381B5BE-D9BF-41E6-8FF2-1AEF927834CB';
    const WALL_WIDTH = '99B2881E-F250-4731-91E1-63102DE150DB';
    const KNT_HEAT_CONDUCT = 'AB8326AA-21F8-45BF-B575-BC035A3D5CB3';
    const STAGE_COUNT = '25885EBA-C790-4B10-BBF5-28F7F6CE2B67';
    const KNT_ROOF = '9E5072FA-60CD-44DC-9350-26EA77366233';
    const KNT_WINDOW = 'D5A8AD9A-8BB4-439D-9DA7-A2EF6C5C8184';
    const SUM_AVG_HEAT = '4AB9304B-4B83-4E61-AE65-2E8A2E2E2B3F';
    const PERSONAL_CNT = '6FAE233F-BD77-4E5C-B299-3276452B7001';

    const DOOR_EFFICIENCY = 'F620DB95-ACC1-4402-884A-7DC0A083907A';
    const HEAT_REGULATOR = '77B737E0-3173-4808-A768-DBA8113F5E39';
    const FLOOR_EFFICIENCY = '3D09079D-44FC-4D7E-948C-84C515AF91D3';
    const AITP = 'A1892F7C-75AD-43E7-9F60-24247A1DE222';
    const PANEL = 'CEED2D15-84DD-413D-9242-87AD3ADF2406';
    const BASEMENT_EFFICIENCY = '6435BCB0-B754-4787-9179-11C868232C72';
    const OUTDOOR_LIGHT = '9FA7BD6A-7145-413D-AE94-5E9D148F924E';
    const INDOOR_LIGHT = '0376C552-8CB4-4BB3-87FE-F911C37704D4';
    const MOVEMENT_LIGHT = '1129512C-0789-4D82-B252-888FA45CB790';
    const WINDOWS_EFFICIENCY = 'CE4A6A05-4A29-429D-B1CF-873B2834EF6F';
    const PIPES_RADIATORS = '34D0CF5B-40FE-4EAA-B3AA-A514ED779BE9';
    const HEAT_EFFICIENCY = 'B3FFEA81-3BF9-44CE-AB96-DD1A3EFFD764';
    const WALL_EFFICIENCY = '5B3D0323-075E-4077-989D-ADB5ACA83373';
    const BOILER_EFFICIENCY = '3FC7D524-C4EA-49AF-8078-E0F53286A308';
    const HEAT_GENERATION_EFFICIENCY = 'C96FBD25-2F57-460A-9856-5558928DD455';
    const BALANCE = 'E4E5F169-6B12-4BB0-BA52-D9481068A499';
    const POWER_STATUS = 'B81389BD-DEA2-4EEC-9344-296BE6F49C39';
    const PIPE_STATUS = 'AF7ABDF7-4E25-4C54-B405-87529B553F62';
    const HEAT_COUNTER = 'D3695667-4509-4894-8F39-768BD12476AC';
    const HEAT_PIPE_INSULATION = 'AD624036-C797-4E92-BBCB-21B7D0A2B028';
    const WINDOW_INSULATION = 'FFFF4036-C797-4E92-BBCB-21B7D0A2B028';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'parameter_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['uuid', 'title'], 'required'],
            [['createdAt', 'changedAt'], 'safe'],
            [['uuid'], 'string', 'max' => 50],
            [['title'], 'string', 'max' => 100],
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
     * @inheritdoc
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
}
