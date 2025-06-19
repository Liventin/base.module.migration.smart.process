<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Migration\SmartProcess;

use Base\Module\Service\LazyService;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessEntity;
use Base\Module\Service\Migration\SmartProcess\MigrateSmartProcessService as IMigrateSmartProcessService;
use Bitrix\Crm\Model\Dynamic\TypeTable;
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

            }
        }
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