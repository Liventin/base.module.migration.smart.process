<?php

namespace Base\Module\Options\TabMigration;

use Base\Module\Exception\ModuleException;
use Base\Module\Options\TabMigration;
use Base\Module\Service\Container;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessService as IMigrateSmartProcessService;
use Base\Module\Service\Options\Option;
use Base\Module\Service\Options\OptionsService;
use Base\Module\Src\Options\Providers\TableProvider;
use Bitrix\Main\Localization\Loc;

class SmartProcessRegistry implements Option
{
    public static function getId(): string
    {
        return 'smart_process_registry';
    }

    public static function getName(): string
    {
        return Loc::getMessage('MODULE_OPTION_SMART_PROCESS_REGISTRY_TITLE');
    }

    public static function getType(): string
    {
        return 'table';
    }

    public static function getTabId(): string
    {
        return TabMigration::getId();
    }

    public static function getSort(): int
    {
        return 400;
    }

    /**
     * @return array
     * @throws ModuleException
     */
    public static function getParams(): array
    {
        /** @var OptionsService $srvOptions */
        $srvOptions = Container::get(OptionsService::SERVICE_CODE);
        /** @var TableProvider $provider */
        $provider = $srvOptions->getProvider(self::getType());

        if (!$provider) {
            return [];
        }

        /** @var IMigrateSmartProcessService $smartProcessService */
        $smartProcessService = Container::get(IMigrateSmartProcessService::SERVICE_CODE);

        $rows = [];
        foreach ($smartProcessService->getSmartProcessStatus() as $smartProcess) {
            $rows[] = [
                'cells' => [
                    [
                        'text' => Loc::getMessage(
                            $smartProcess['exists']
                                ? 'MODULE_OPTION_SMART_PROCESS_REGISTRY_STATUS_YES'
                                : 'MODULE_OPTION_SMART_PROCESS_REGISTRY_STATUS_NO'
                        ),
                        'status' => $smartProcess['exists'] ? 'ok' : 'no',
                    ],
                    $smartProcess['title'],
                    $smartProcess['code'],
                ],
                'highlight' => !$smartProcess['exists'],
            ];
        }

        return $provider
            ->setColumns([
                Loc::getMessage('MODULE_OPTION_SMART_PROCESS_REGISTRY_COL_STATUS'),
                Loc::getMessage('MODULE_OPTION_SMART_PROCESS_REGISTRY_COL_TITLE'),
                Loc::getMessage('MODULE_OPTION_SMART_PROCESS_REGISTRY_COL_CODE'),
            ])
            ->setRows($rows)
            ->setEmpty(Loc::getMessage('MODULE_OPTION_SMART_PROCESS_REGISTRY_EMPTY'))
            ->getParamsToArray();
    }
}