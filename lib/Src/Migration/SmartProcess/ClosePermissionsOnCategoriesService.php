<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Migration\SmartProcess;

use Base\Module\Service\LazyService;
use Base\Module\Service\Migration\SmartProcess\ClosePermissionsOnCategoriesService as IClosePermissionsOnCategoriesService;
use Bitrix\Crm\Category\PermissionEntityTypeHelper;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use CCrmRole;
use Exception;

#[LazyService(serviceCode: IClosePermissionsOnCategoriesService::SERVICE_CODE, constructorParams: [])]
class ClosePermissionsOnCategoriesService implements IClosePermissionsOnCategoriesService
{
    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('crm');
    }

    /**
     * @inheritdoc
     * @throws ArgumentException
     * @throws SystemException
     * @throws Exception
     */
    public function closeForNotAdminRoles(int $entityTypeId): void
    {
        $permissionEntities = (new PermissionEntityTypeHelper($entityTypeId))
            ->getAllPermissionEntityTypesForEntity();

        foreach ($permissionEntities as $permissionEntity) {
            CCrmRole::EraseEntityPermissionsForNotAdminRoles($permissionEntity);
        }
    }
}