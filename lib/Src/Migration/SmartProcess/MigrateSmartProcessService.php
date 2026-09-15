<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Migration\SmartProcess;

use Base\Module\Service\Container;
use Base\Module\Service\LazyService;
use Base\Module\Service\Migration\SmartProcess\ClosePermissionsOnCategoriesService as IClosePermissionsOnCategoriesService;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessEntity;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessService as IMigrateSmartProcessService;
use Bitrix\Crm\Model\Dynamic\TypeTable;
use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\SystemException;
use Exception;

#[LazyService(serviceCode: IMigrateSmartProcessService::SERVICE_CODE, constructorParams: [])]
class MigrateSmartProcessService implements IMigrateSmartProcessService
{
    /**
     * @var MigrateSmartProcessEntity[]
     */
    public array $smartProcessList = [];

    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('crm');
    }

    public function setSmartProcessList(array $smartProcessList): self
    {
        $this->smartProcessList = $smartProcessList;
        return $this;
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     * @throws Exception
     */
    public function install(): void
    {
        if (empty($this->smartProcessList)) {
            return;
        }

        $enabled = $this->getEnabledSmartProcess();
        
        foreach ($this->smartProcessList as $entity) {
            $name = $entity::getName();
            $entityParams = $entity::getParams();
            $entityParams['CODE'] = $entity::getCode();

            if (array_key_exists($name, $enabled)) {
                $isDiff = false;
                foreach ($entityParams as $param => $value) {
                    if ($enabled[$name][$param] !== $value) {
                        $isDiff = true;
                        break;
                    }
                }
                if ($isDiff) {
                    TypeTable::update((int)$enabled[$name]['ID'], $entityParams);
                }
            } else {
                $entityParams['NAME'] = $name;
                $entityParams['TITLE'] = $entity::getTitle();
                $entityParams['ENTITY_TYPE_ID'] = TypeTable::getNextAvailableEntityTypeId();

                TypeTable::add($entityParams);

                // Если доступ к воронкам при создании закрыт (IS_SET_OPEN_PERMISSIONS = N),
                // ядро Bitrix всё равно выдаёт не-администраторским ролям права на новую воронку
                // (пресеты по коду роли: MANAGER/DEPUTY/HEAD/OBSERVER).
                // Закрываем доступ фоновой задачей ПОСЛЕ задачи ядра, чтобы перекрыть её выдачу.
                if ($entityParams['IS_SET_OPEN_PERMISSIONS'] === 'N') {
                    $this->scheduleClosePermissions((int)$entityParams['ENTITY_TYPE_ID']);
                }
            }
        }
    }

    /**
     * Ставит фоновую задачу закрытия прав на воронки смарт-процесса для не-админских ролей.
     * Выполняется после фоновой задачи ядра (DefaultCategoryPermissions), которая
     * автоматически выдаёт права ролям при создании воронки.
     *
     * @param int $entityTypeId Entity type id смарт-процесса.
     * @throws ArgumentException
     * @throws SystemException
     */
    private function scheduleClosePermissions(int $entityTypeId): void
    {
        /** @var IClosePermissionsOnCategoriesService $closePermissionsService */
        $closePermissionsService = Container::get(IClosePermissionsOnCategoriesService::SERVICE_CODE);

        Application::getInstance()->addBackgroundJob(
            [$closePermissionsService, 'closeForNotAdminRoles'],
            [$entityTypeId]
        );
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public function reInstall(): void
    {
        $this->install();
    }

    public function getParamsConstructor(): ParamsConstructor
    {
        return new ParamsConstructor();
    }

    /**
     * @throws SystemException
     * @throws ArgumentException
     */
    public function getEnabledSmartProcess(): array
    {
        $names = [];
        foreach ($this->smartProcessList as $item) {
            $names[] = $item::getName();
        }

        /** @var Query $query */
        $query = TypeTable::query();

        return array_column(
            $query->addSelect('*')
                ->whereIn('NAME', $names)
                ->fetchAll(),
            null,
            'NAME'
        );
    }
}