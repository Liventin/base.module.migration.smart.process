<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Migration\SmartProcess;

use Base\Module\Service\LazyService;
use Base\Module\Service\Migration\SmartProcess\ClosePermissionsOnCategoriesService as IClosePermissionsOnCategoriesService;
use Bitrix\Crm\Category\PermissionEntityTypeHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CCrmRole;

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
     */
    public function closePermissions(int $entityTypeId): void
    {
        $permissionEntities = (new PermissionEntityTypeHelper($entityTypeId))
            ->getAllPermissionEntityTypesForEntity();

        foreach ($permissionEntities as $permissionEntity) {
            CCrmRole::EraseEntityPermissons($permissionEntity);
        }
    }
}