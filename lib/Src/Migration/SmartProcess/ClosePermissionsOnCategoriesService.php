<?php

namespace Base\Module\Src\Migration\SmartProcess;

use Bitrix\Crm\Category\PermissionEntityTypeHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CCrmRole;

class ClosePermissionsOnCategoriesService
{
    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('crm');
    }

    public function closePermissions(int $entityTypeId): void
    {
        $permissionEntities = (new PermissionEntityTypeHelper($entityTypeId))
            ->getAllPermissionEntityTypesForEntity();

        foreach ($permissionEntities as $permissionEntity) {
            CCrmRole::EraseEntityPermissons($permissionEntity);
        }
    }
}