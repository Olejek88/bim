<?php
return [
    'controllerMap' => [
        'daemon' => [
            'class' => 'inpassor\daemon\Controller',
            'uid' => 'daemon', // The daemon UID. Giving daemons different UIDs makes possible to run several daemons.
            'pidDir' => '@console/runtime/daemon',
            'logsDir' => '@console/runtime/daemon/logs',
            'clearLogs' => false, // Clear log files on start.
            'workersMap' => [
                'politer_service' => [
                    'class' => 'console\workers\PoliterService',
                    'active' => true,
                    'maxProcesses' => 1,
                    'delay' => 120,
                ],
            ],
        ],
    ],
];
