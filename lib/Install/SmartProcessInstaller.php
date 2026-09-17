<?php

namespace Base\Module\Install;

use Base\Module\Install\Interface\Install;
use Base\Module\Install\Interface\ReInstall;
use Base\Module\Service\Container;
use Base\Module\Exception\ModuleException;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessEntity;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessService as IMigrateSmartProcessService;
use Base\Module\Service\Tool\ClassList;

class SmartProcessInstaller implements Install, ReInstall
{
    /**
     * @return array
     * @throws ModuleException
     */
    private function getSmartProcessList(): array
    {
        /** @var ClassList $classList */
        $classList = Container::get(ClassList::SERVICE_CODE);
        return $classList->setSubClassesFilter([MigrateSmartProcessEntity::class])->getFromLib('Migration');
    }

    /**
     * @throws ModuleException
     */
    public function install(): void
    {
        /** @var IMigrateSmartProcessService $smartProcessService */
        $smartProcessService = Container::get(IMigrateSmartProcessService::SERVICE_CODE);
        $smartProcessService->setSmartProcessList($this->getSmartProcessList())->install();
    }

    /**
     * @throws ModuleException
     */
    public function reInstall(): void
    {
        /** @var IMigrateSmartProcessService $smartProcessService */
        $smartProcessService = Container::get(IMigrateSmartProcessService::SERVICE_CODE);
        $smartProcessService->setSmartProcessList($this->getSmartProcessList())->reInstall();
    }

    public function getInstallSort(): int
    {
        return 250;
    }

    public function getReInstallSort(): int
    {
        return 250;
    }
}
