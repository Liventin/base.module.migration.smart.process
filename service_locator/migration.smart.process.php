<?php
defined('B_PROLOG_INCLUDED') || die;

use Base\Module\Src\Migration\SmartProcess\MigrateSmartProcessService;

return [
    'base.module.migration.smart.process' => [
        'className' => MigrateSmartProcessService::class,
        'constructorParams' => [],
    ],
];
