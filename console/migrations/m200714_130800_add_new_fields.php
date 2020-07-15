<?php

use common\components\MainFunctions;
use common\models\EventType;
use common\models\ParameterType;
use yii\db\Migration;

/**
 * Class m200714_130800_add_new_fields
 */
class m200714_130800_add_new_fields extends Migration
{
    const EVENT_TYPE = '{{%event_type}}';

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->addColumn('event', 'status', $this->integer()->defaultValue(0));
        $this->addColumn('event', 'date', $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));

        self::insertRefs('parameter_type', 'Цель потребления', ParameterType::TARGET_CONSUMPTION);
        self::insertRefs('parameter_type', 'Энергоэффективность', ParameterType::ENERGY_EFFICIENCY);
        self::insertRefs('parameter_type', 'Энергооснащенность', ParameterType::POWER_EQUIPMENT);

        $this->createTable(self::EVENT_TYPE, [
            '_id' => $this->primaryKey(),
            'uuid' => $this->string(36)->notNull()->unique(),
            'title' => $this->string()->notNull(),
            'createdAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
            'changedAt' => $this->timestamp()->notNull()->defaultExpression('CURRENT_TIMESTAMP'),
        ], $tableOptions);

        self::insertRefs(self::EVENT_TYPE, 'Общий', EventType::COMMON);

        self::insertRefs(self::EVENT_TYPE, 'Промывка отопительной системы внутри дома', null);
        self::insertRefs(self::EVENT_TYPE, 'Балансировка отопительной системы и стояков', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление дверных проемов в подъездах', null);
        self::insertRefs(self::EVENT_TYPE, 'Монтаж доводчиков дверей', null);
        self::insertRefs(self::EVENT_TYPE, 'Инфракрасная съемка фасадов зданий', null);
        self::insertRefs(self::EVENT_TYPE, 'Обследование системы отопления', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка теплоотражающих экранов за отопительными приборами', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка регуляторов отопления', null);

        self::insertRefs(self::EVENT_TYPE, 'Утепление черных полов', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка автоматических узлов управления отоплением', null);
        self::insertRefs(self::EVENT_TYPE, 'Герметизация и утепление межпанельных стыков', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление чердаков и подвалов', null);
        self::insertRefs(self::EVENT_TYPE, 'Восстановление циркуляционных систем ГВС', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена светильников уличного освещения на энергоффективные', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка светодиодных ламп', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка датчиков движения в местах общего пользования', null);

        self::insertRefs(self::EVENT_TYPE, 'Замена старых окон на современные', null);
        self::insertRefs(self::EVENT_TYPE, 'Модернизация систем отопления и установкой новых радиаторов', null);
        self::insertRefs(self::EVENT_TYPE, 'Модернизация ИТП', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка энергоэффективных отопительных котлов', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление ограждающих конструкций домов и мест общего пользования', null);
        self::insertRefs(self::EVENT_TYPE, 'Модернизация котельных с использованием энергоэффективного оборудования', null);
        self::insertRefs(self::EVENT_TYPE, 'Внедрение систем автоматизации работы и загрузки котлов', null);
        self::insertRefs(self::EVENT_TYPE, 'Автоматизация отпуска тепловой энергии потребителям', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена тепловых сетей с использованием энергоэффективного оборудования', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена устаревшей тепловой изоляции на трубопроводах', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка регулируемого привода в системах водоснабжения и водоотведения', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка частотно-регулируемого привода на насосном оборудовании', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена электрических сетей', null);

        self::insertRefs(self::EVENT_TYPE, 'Установка балансировочных вентилей', null);
        self::insertRefs(self::EVENT_TYPE, 'Балансировка системы отопления', null);
        self::insertRefs(self::EVENT_TYPE, 'Промывка трубопроводов и стояков системы отопления', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка общедомового счетчика тепла и горячей воды', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка поквартирных счетчиков тепла и горячей воды', null);

        self::insertRefs(self::EVENT_TYPE, 'Установка индивидуального теплового пункта', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка теплообменника отопления', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка автоматической системы управления отоплением и в ГВС', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена трубопроводов', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена арматуры', null);
        self::insertRefs(self::EVENT_TYPE, 'Теплоизоляция трубопроводов в подвалах, на чердаках и в местах общего пользования', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка терморегулирующих клапанов на отопительных приборах', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка запорных вентилей', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка и модернизация насосов для обеспечения рециркуляции воды в системах ГВС', null);

        self::insertRefs(self::EVENT_TYPE, 'Установка общедомовых и индивидуальных счетчиков элетроэнергии', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка энергосберегающих ламп в местах общего пользования', null);

        self::insertRefs(self::EVENT_TYPE, 'Установка автоматической регулировки освещения', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена электродвигателей', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка приводов частотного регулирования', null);

        self::insertRefs(self::EVENT_TYPE, 'Заделка, уплотнение и утепление дверей подъездов', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка дверей и заслонок в подвалах', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка дверей и заслонок на чердаках', null);
        self::insertRefs(self::EVENT_TYPE, 'Заделка и уплотнение окон в местах общего пользования', null);

        self::insertRefs(self::EVENT_TYPE, 'Установка теплоотражающих пленок на окна', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка низкоэмиссионных стекол на окна в подъездах', null);
        self::insertRefs(self::EVENT_TYPE, 'Замена оконных и дверных блоков на энергоэффективные', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление пола и стен подвала', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление пола чердака', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление крыши', null);
        self::insertRefs(self::EVENT_TYPE, 'Заделка межпанельных швов', null);
        self::insertRefs(self::EVENT_TYPE, 'Утепление стен', null);
        self::insertRefs(self::EVENT_TYPE, 'Остекление балконов и лоджий', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка воздушных заслонок в системе вентиляции', null);

        self::insertRefs(self::EVENT_TYPE, 'Установка балансировочных вентилей', null);
        self::insertRefs(self::EVENT_TYPE, 'Периодическая балансировка системы отопления', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка дверей в проемах подвальных помещений', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка дверей в проемах чердачных помещений', null);
        self::insertRefs(self::EVENT_TYPE, 'Заделка и уплотнение оконных блоков', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка приборов учета тепловой энергии', null);
        self::insertRefs(self::EVENT_TYPE, 'Установка локальных систем регулирования отоплением', null);
        self::insertRefs(self::EVENT_TYPE, 'Оптимизация гидравлических режимов теплосетей', null);
        self::insertRefs(self::EVENT_TYPE, 'Сокращение потерь из-за незаконных сливов теплоносителя', null);

        //self::insertRefs(self::EVENT_TYPE, 'Модернизация котельных и ЦТП', null);
        //self::insertRefs(self::EVENT_TYPE, 'Реконструкция и строительство новых тепловых сетей', null);
        //self::insertRefs(self::EVENT_TYPE, 'Возведение новых котельных', null);
        //self::insertRefs(self::EVENT_TYPE, 'Модернизацией системы отопления в домах и квартирах', null);
        //self::insertRefs(self::EVENT_TYPE, 'Установка современной качественной теплоизоляции', null);

        $this->addColumn('event', 'eventTypeUuid', $this->string(36)->notNull()->defaultValue(EventType::COMMON));
    }

    private function insertRefs($table, $title, $uuid)
    {
        $date = date('Y-m-d\TH:i:s');
        if (!$uuid) {
            $uuid = MainFunctions::GUID();
        }
        $this->insert($table, [
            'uuid' => $uuid,
            'title' => $title,
            'createdAt' => $date,
            'changedAt' => $date,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('event', 'status');
        $this->dropColumn('event', 'date');
        return true;
    }
}
