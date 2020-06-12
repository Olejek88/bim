<?php

use common\models\EquipmentStatus;
use common\models\ObjectSubType;
use common\models\Settings;
use yii\db\Migration;

/**
 * Class m200604_152551_base_schema_v1_creation
 */
class m200604_152551_base_schema_v1_creation extends Migration
{
    const OBJECT = '{{%object}}';
    const OBJECT_DISTRICT = '{{%object_district}}';
    const DISTRICT_COORDINATES = '{{%district_coordinates}}';

    const EQUIPMENT = '{{%equipment}}';
    const MEASURE_CHANNEL = '{{%measure_channel}}';
    const MEASURE_TYPE = '{{%measure_type}}';
    const MEASURE = '{{%measure}}';
    const PARAMETER = '{{%parameter}}';
    const PARAMETER_TYPE = '{{%parameter_type}}';
    const BALANCE_POINT = '{{%balance_point}}';

    const EQUIPMENT_TYPE = '{{%equipment_type}}';
    const EQUIPMENT_STATUS = '{{%equipment_status}}';
    const OBJECT_TYPE = '{{%object_type}}';
    const OBJECT_SUB_TYPE = '{{%object_sub_type}}';
    const EVENT = '{{%event}}';
    const ATTRIBUTE = '{{%attribute}}';
    const ATTRIBUTE_TYPE = '{{%attribute_type}}';

    const ALARM = '{{%alarm}}';
    const SETTINGS = '{{%settings}}';
    const REGISTER = '{{%register}}';
    const ACTION_REGISTER = '{{%action_register}}';
    const SERVICE_REGISTER = '{{%service_register}}';

    const USER = '{{%user}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable(self::OBJECT_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::OBJECT_SUB_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::OBJECT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'parentUuid' => $this->string(36)->null()->defaultValue(null),
            'objectTypeUuid' => $this->string(36)->notNull(),
            'objectSubTypeUuid' => $this->string(36)->notNull()->defaultValue(ObjectSubType::PLACEMENT),
            'latitude' => $this->double()->defaultValue('55.15'),
            'longitude' => $this->double()->defaultValue('61.13'),
            'fiasGuid' => $this->string(),
            'fiasParentGuid' => $this->string(),
            'okato' => $this->string(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-objectTypeUuid',
            self::OBJECT,
            'objectTypeUuid'
        );

        $this->addForeignKey(
            'fk-object-objectTypeUuid',
            self::OBJECT,
            'objectTypeUuid',
            self::OBJECT_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-subTypeUuid',
            self::OBJECT,
            'objectSubTypeUuid'
        );

        $this->addForeignKey(
            'fk-object-objectSubTypeUuid',
            self::OBJECT,
            'objectSubTypeUuid',
            self::OBJECT_SUB_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-parentUuid',
            self::OBJECT,
            'parentUuid'
        );

        $this->createTable(self::OBJECT_DISTRICT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'districtUuid' => $this->string(36)->notNull(),
            'objectUuid' => $this->string(36)->notNull(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-districtUuid',
            self::OBJECT_DISTRICT,
            'districtUuid'
        );

        $this->addForeignKey(
            'fk-object_district-districtUuid',
            self::OBJECT_DISTRICT,
            'districtUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-objectUuid',
            self::OBJECT_DISTRICT,
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-object_district-objectUuid',
            self::OBJECT_DISTRICT,
            'objectUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::DISTRICT_COORDINATES, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'districtUuid' => $this->string(36)->notNull(),
            'coordinates' => $this->text()->notNull(), // JSON массив координат
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-districtUuid',
            self::DISTRICT_COORDINATES,
            'districtUuid'
        );

        $this->addForeignKey(
            'fk-district_coordinates-districtUuid',
            self::DISTRICT_COORDINATES,
            'districtUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::EQUIPMENT_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::EQUIPMENT_STATUS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::EQUIPMENT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string(150)->notNull(),
            'objectUuid' => $this->string(36)->notNull(),
            'equipmentTypeUuid' => $this->string(36)->notNull(),
            'equipmentStatusUuid' => $this->string(36)->notNull()->defaultValue(EquipmentStatus::UNKNOWN),
            'serial' => $this->string(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-objectUuid',
            self::EQUIPMENT,
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-equipment-objectUuid',
            self::EQUIPMENT,
            'objectUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentTypeUuid',
            self::EQUIPMENT,
            'equipmentTypeUuid'
        );

        $this->addForeignKey(
            'fk-equipment-equipmentTypeUuid',
            self::EQUIPMENT,
            'equipmentTypeUuid',
            self::EQUIPMENT_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-equipmentStatusUuid',
            self::EQUIPMENT,
            'equipmentStatusUuid'
        );

        $this->addForeignKey(
            'fk-equipment-equipmentStatusUuid',
            self::EQUIPMENT,
            'equipmentStatusUuid',
            self::EQUIPMENT_STATUS,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::MEASURE_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createTable(self::MEASURE_CHANNEL, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'equipmentUuid' => $this->string(36)->notNull(),
            'measureTypeUuid' => $this->string(36)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createIndex(
            'idx-equipmentUuid',
            self::MEASURE_CHANNEL,
            'equipmentUuid'
        );

        $this->addForeignKey(
            'fk-measure_channel-equipmentUuid',
            self::MEASURE_CHANNEL,
            'equipmentUuid',
            self::EQUIPMENT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-measureTypeUuid',
            self::MEASURE_CHANNEL,
            'measureTypeUuid'
        );

        $this->addForeignKey(
            'fk-measure_channel-measureTypeUuid',
            self::MEASURE_CHANNEL,
            'measureTypeUuid',
            self::MEASURE_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::MEASURE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'measureChannelUuid' => $this->string(36)->notNull(),
            'value' => $this->double()->defaultValue(0),
            'type' => $this->tinyInteger()->defaultValue(0)->notNull(),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-measureChannelUuid',
            self::MEASURE,
            'measureChannelUuid'
        );

        $this->addForeignKey(
            'fk-measure-measureChannelUuid',
            self::MEASURE,
            'measureChannelUuid',
            self::MEASURE_CHANNEL,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::PARAMETER_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP')
        ], $tableOptions);

        $this->createTable(self::PARAMETER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'entityUuid' => $this->string(36)->notNull(),
            'parameterTypeUuid' => $this->string(36)->notNull(),
            'value' => $this->double()->defaultValue(0),
            'date' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-parameterTypeUuid',
            self::PARAMETER,
            'parameterTypeUuid'
        );

        $this->addForeignKey(
            'fk-parameter-parameterTypeUuid',
            self::PARAMETER,
            'parameterTypeUuid',
            self::PARAMETER_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::BALANCE_POINT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'measureChannelUuid' => $this->string(36)->notNull(),
            'objectUuid' => $this->string(36)->notNull(),
            'input' => $this->tinyInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-measureChannelUuid',
            self::BALANCE_POINT,
            'measureChannelUuid'
        );

        $this->addForeignKey(
            'fk-balance_point-measureChannelUuid',
            self::BALANCE_POINT,
            'measureChannelUuid',
            self::MEASURE_CHANNEL,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createIndex(
            'idx-objectUuid',
            self::BALANCE_POINT,
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-balance_point-objectUuid',
            self::BALANCE_POINT,
            'objectUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::EVENT, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'description' => $this->text()->notNull(),
            'objectUuid' => $this->string(36)->notNull(),
            'deleted' => $this->smallInteger()->defaultValue(0),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-objectUuid',
            self::EVENT,
            'objectUuid'
        );

        $this->addForeignKey(
            'fk-event-objectUuid',
            self::EVENT,
            'objectUuid',
            self::OBJECT,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::ATTRIBUTE_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::ATTRIBUTE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'attributeTypeUuid' => $this->string(36)->notNull(),
            'entityUuid' => $this->string(36)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-attributeTypeUuid',
            self::ATTRIBUTE,
            'attributeTypeUuid'
        );

        $this->addForeignKey(
            'fk-attribute-attributeTypeUuid',
            self::ATTRIBUTE,
            'attributeTypeUuid',
            self::ATTRIBUTE_TYPE,
            'uuid',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->createTable(self::ALARM, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'entityUuid' => $this->string(36)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::REGISTER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'type' => $this->integer()->defaultValue(0),
            'entityUuid' => $this->string(36)->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::ACTION_REGISTER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'type' => $this->integer()->defaultValue(0),
            'userId' => $this->integer()->notNull(),
            'entityUuid' => $this->string(45),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createIndex(
            'idx-userId',
            self::ACTION_REGISTER,
            'userId'
        );

        $this->addForeignKey(
            'fk-action_register-userId',
            self::ACTION_REGISTER,
            'userId',
            self::USER,
            'id',
            $delete = 'RESTRICT',
            $update = 'CASCADE'
        );

        $this->addColumn('user', 'deleted', $this->boolean()->defaultValue(false));
        $this->addColumn('user', 'type', $this->integer()->defaultValue(2));
        $this->addColumn('user', 'name', $this->string());
        $this->addColumn('user', 'image', $this->string());

        $this->createTable(self::SETTINGS, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'parameter' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        $this->createTable(self::SERVICE_REGISTER, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string()->notNull()->unique(),
            'service' => $this->string()->notNull(),
            'type' => $this->integer()->defaultValue(0),
            'view' => $this->integer()->defaultValue(0),
            'description' => $this->string(),
            'entityUuid' => $this->string(),
            'createdAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->dateTime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200604_152551_base_schema_v1_creation cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200604_152551_base_schema_v1_creation cannot be reverted.\n";

        return false;
    }
    */
}
