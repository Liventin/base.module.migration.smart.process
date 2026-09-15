<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Migration\SmartProcess;

use Base\Module\Service\LazyService;
use Base\Module\Service\Migration\SmartProcess\ClosePermissionsOnCategoriesService as IClosePermissionsOnCategoriesService;
use Bitrix\Crm\Category\CategoryPermissionsManager;
use Bitrix\Crm\Category\PermissionEntityTypeHelper;
use Bitrix\Crm\CategoryIdentifier;
use Bitrix\Crm\Security\Role\Model\RolePermissionTable;
use Bitrix\Crm\Service\UserPermissions;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
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
            $categoryId = (new PermissionEntityTypeHelper($entityTypeId))
                ->extractCategoryFromPermissionEntityType($permissionEntity);

            $this->closeForCategory($entityTypeId, $categoryId);
        }
    }

    /**
     * Закрывает доступ к воронке для всех ролей, кроме администраторских.
     *
     * Используется штатный API ядра CategoryPermissionsManager::setPermissions()
     * с уровнем PERMISSION_NONE (минимальный пресет): он сам пропускает
     * администраторских и системные роли, а права записывает через ORM.
     *
     * Перед вызовом сбрасываем ORM-кэш ролей, чтобы определение админов
     * (RolePermission::getAll) читало свежие данные.
     *
     * @param int $entityTypeId
     * @param int $categoryId
     * @throws ArgumentException
     * @throws SystemException
     */
    private function closeForCategory(int $entityTypeId, int $categoryId): void
    {
        RolePermissionTable::cleanCache();

        $categoryIdentifier = new CategoryIdentifier($entityTypeId, $categoryId);

        CategoryPermissionsManager::getInstance()->setPermissions(
            $categoryIdentifier,
            UserPermissions::PERMISSION_NONE
        );
    }
}