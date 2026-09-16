<?php

namespace Base\Module\Options\TabMigration;

use Base\Module\Options\TabMigration;
use Base\Module\Service\Options\Option;
use Bitrix\Main\Localization\Loc;

class SeparatorSmartProcessRegistry implements Option
{
    public static function getId(): string
    {
        return 'smart_process_separator';
    }

    public static function getName(): string
    {
        return Loc::getMessage('MODULE_OPTION_SMART_PROCESS_SEPARATOR_TITLE');
    }

    public static function getType(): string
    {
        return 'separator';
    }

    public static function getTabId(): string
    {
        return TabMigration::getId();
    }

    public static function getSort(): int
    {
        return 300;
    }

    public static function getParams(): array
    {
        return [];
    }
}
