<?php

namespace Base\Module\Src\Migration\SmartProcess;

use Bitrix\Crm\Category\PermissionEntityTypeHelper;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use CCrmRole;

class ClosePermissionsOnCategoriesService
{
    private static ?self $instance = null;

    /**
     * @throws LoaderException
     */
    private function __construct()
    {
        Loader::requireModule('crm');
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
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