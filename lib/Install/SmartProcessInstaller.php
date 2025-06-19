<?php

namespace Base\Module\Install;

use Base\Module\Install\Interface\Install;
use Base\Module\Install\Interface\ReInstall;
use Base\Module\Service\Container;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessEntity;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessService as IMigrateSmartProcessService;
use Base\Module\Service\Tool\ClassList;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\SystemException;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class SmartProcessInstaller implements Install, ReInstall
{
    /**
     * @return array
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    private function getSmartProcessList(): array
    {
        /** @var ClassList $classList */
        $classList = Container::get(ClassList::SERVICE_CODE);
        return $classList->setSubClassesFilter([MigrateSmartProcessEntity::class])->getFromLib('Migration');
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    public function install(): void
    {
        /** @var IMigrateSmartProcessService $userFieldService */
        $userFieldService = Container::get(IMigrateSmartProcessService::SERVICE_CODE);
        $userFieldService->setSmartProcessList($this->getSmartProcessList())->install();
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    public function reInstall(): void
    {
        /** @var IMigrateSmartProcessService $userFieldService */
        $userFieldService = Container::get(IMigrateSmartProcessService::SERVICE_CODE);
        $userFieldService->setSmartProcessList($this->getSmartProcessList())->reInstall();
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
