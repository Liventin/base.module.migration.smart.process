<?php

namespace Base\Module\Service\Migration\SmartProcess;

interface MigrateSmartProcessEntity
{
    public static function getName(): string;
    public static function getTitle(): string;
    public static function getCode(): string;
    public static function getParams(): array;
}