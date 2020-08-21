<?phpuse common\models\User;$currentUser = Yii::$app->view->params['currentUser'];$userImage = $currentUser->getImageUrl();?><aside class="main-sidebar">    <section class="sidebar">        <!-- Sidebar user panel -->        <div class="user-panel">            <div class="pull-left image">                <?php                echo '<img src="' . $userImage . '" class="img-circle" alt="U">';                ?>            </div>            <div class="pull-left info">                <p><?php if ($currentUser) echo $currentUser['name']; ?> </p>                <a href="#"><i class="fa fa-circle text-success"></i>                    <?php echo Yii::t('app', ' Online') ?></a>            </div>        </div>        <?php        echo dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    ['label' => Yii::t('app', 'Система'), 'options' => ['class' => 'header']],                    [                        "label" => Yii::t('app', 'Объекты'),                        'icon' => 'glyphicon glyphicon-home',                        'template' => '<a href="{url}">{icon} {label}<span class="pull-right-container"><small class="label pull-right bg-green">new</small></span></a>',                        "items" => [                            ["label" => Yii::t('app', 'Объекты'), 'icon' => 'fa fa-home', "url" => ["/object/tree"]],                            ["label" => Yii::t('app', 'Таблицей'), 'icon' => 'fa fa-table', "url" => ["/object"]],                            ["label" => Yii::t('app', 'Гипертаблица'), 'icon' => 'fa fa-table', "url" => ["/object/table"]],                            ["label" => Yii::t('app', 'Пользователи'), 'icon' => 'fa fa-user', "url" => ["/user"]],                            ["label" => Yii::t('app', 'Журнал объектов'), 'icon' => 'fa fa-list', "url" => ["/register"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu'],                'items' => [                    ['label' => Yii::t('app', 'Карты'), 'url' => ['site/index'], 'visible' => Yii::$app->user->isGuest],                    [                        "label" => Yii::t('app', 'События'),                        'icon' => 'fa fa-map',                        "items" => [                            ["label" => Yii::t('app', 'Карта'), 'icon' => 'fa fa-map-marker', 'url' => '/site/index'],                            ["label" => Yii::t('app', 'Картограмма'), 'icon' => 'fa fa-map', 'url' => '/site/map']                        ],                    ],                    [                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    ['label' => Yii::t('app', 'События'), 'options' => ['class' => 'header']],                    [                        "label" => Yii::t('app', 'События'),                        'icon' => 'glyphicon glyphicon-equalizer',                        "items" => [                            ["label" => Yii::t('app', 'Мероприятия'), 'icon' => 'fa fa-table', "url" => ["/event"]],                            ["label" => Yii::t('app', 'Календарь мероприятий'), 'icon' => 'fa fa-calendar', "url" => ["/event/plan"]],                            ["label" => Yii::t('app', 'Календарь целей'), 'icon' => 'fa fa-calendar', "url" => ["/object/target"]],//                            ["label" => Yii::t('app', 'Статистика событий'), 'icon' => 'glyphicon glyphicon-stats', "url" => ["/event/pie"]],                            ["label" => Yii::t('app', 'Вычисляемые параметры    '), 'icon' => 'fa fa-list', "url" => ["/parameter"]],                        ],                    ],                ],            ]        ) ?>        <?= dmstr\widgets\Menu::widget(            [                'options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                'items' => [                    ['label' => Yii::t('app', 'Аналитика'), 'options' => ['class' => 'header']],                    [                        "label" => Yii::t('app', 'Аналитика'),                        "icon" => 'fa fa-pie-chart',                        "items" => [                            ['label' => Yii::t('app', 'Календарь целей'), 'icon' => 'fa fa-table', 'url' => '/object/plan'],                            ['label' => Yii::t('app', 'Анализ данных'), 'icon' => 'fa fa-pie-chart', 'url' => '/site/stats',],                            ["label" => Yii::t('app', 'Анализ соблюдения целей'), 'icon' => 'fa fa-dot-circle-o', "url" => ["/object/target?analyse=1"]],                            ['label' => Yii::t('app', 'Эффективность'), 'icon' => 'fa fa-percent', 'url' => '/object/efficiency',],                            ['label' => Yii::t('app', 'Прогноз потребления'), 'icon' => 'fa fa-line-chart', 'url' => '/object/predictive',],                            ['label' => Yii::t('app', 'Пользовательский отчет'), 'icon' => 'fa fa-table', 'url' => '/object/report',],                        ],                    ],                ],            ]        ) ?>        <?php        if (Yii::$app->user->can(User::PERMISSION_ADMIN) || Yii::$app->user->can(User::PERMISSION_OPERATOR))            echo dmstr\widgets\Menu::widget(                ['options' => ['class' => 'sidebar-menu', 'data-widget' => 'tree'],                    'items' => [                        ['label' => Yii::t('app', 'Конфигурация'), 'icon' => 'fa fa-sitemap',                            "items" => [                                ['label' => Yii::t('app', 'Типы объектов'), 'icon' => 'fa fa-server', 'url' => '/object-type/',],                                ['label' => Yii::t('app', 'Типы событий'), 'icon' => 'fa fa-table', 'url' => '/event-type/',],                            ],                        ],                    ],                ]            ) ?>    </section></aside>