<?php

/** @noinspection PhpUnused */

namespace Base\Module\Src\Migration\SmartProcess;

use Base\Module\Service\LazyService;
use Base\Module\Service\Migration\SmartProcess\ClosePermissionsOnCategoriesService as IClosePermissionsOnCategoriesService;
use Bitrix\Crm\Category\PermissionEntityTypeHelper;
use Bitrix\Crm\Security\Role\Manage\Entity\AutomatedSolutionConfig;
use Bitrix\Crm\Service\Container;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use CCrmOwnerType;
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

        $adminRoleIds = $this->getAdminRoleIds($entityTypeId);

        foreach ($permissionEntities as $permissionEntity) {
            $this->erasePermissionsForNotAdminRoles($permissionEntity, $adminRoleIds);
        }
    }

    /**
     * Определяет администраторские роли напрямую по БД (без кэша ядра).
     *
     * Ядро определяет админов через RolePermission::getAdminRolesIds(), который
     * использует кэшированный RolePermission::getAll() (ORM-кэш на 84600 сек).
     * Если кэш устарел (собран до появления CONFIG.WRITE у админ-роли), админ
     * не распознаётся, и удаление прав затирает и админов. Поэтому здесь
     * читаем права напрямую.
     *
     * @param int $entityTypeId Entity type id смарт-процесса.
     * @return int[]
     */
    private function getAdminRoleIds(int $entityTypeId): array
    {
        $adminPermissionEntity = 'CONFIG';
        if (CCrmOwnerType::isPossibleDynamicTypeId($entityTypeId)) {
            $type = Container::getInstance()->getTypeByEntityTypeId($entityTypeId);
            if ($type?->getCustomSectionId()) {
                $adminPermissionEntity = AutomatedSolutionConfig::generateEntity($type->getCustomSectionId());
            }
        }

        $connection = Application::getConnection();

        $rows = $connection->query(
            "SELECT DISTINCT ROLE_ID
               FROM b_crm_role_perms
              WHERE ENTITY = '{$adminPermissionEntity}'
                AND PERM_TYPE = 'WRITE'
                AND ATTR >= 'X'"
        );

        $roleIds = [];
        while ($row = $rows->fetch()) {
            $roleIds[] = (int)$row['ROLE_ID'];
        }

        return $roleIds;
    }

    /**
     * Удаляет права на воронку у всех ролей, кроме заданных (админских).
     *
     * Прямой DELETE (минуя RolePermission::getAll()-кэш), чтобы не зависеть
     * от устаревшего ORM-кэша ядра.
     *
     * @param string $permissionEntity Права-entity воронки (например DYNAMIC_1086_C20).
     * @param int[] $adminRoleIds Роли-админы, которые сохраняются.
     * @return void
     */
    private function erasePermissionsForNotAdminRoles(string $permissionEntity, array $adminRoleIds): void
    {
        $adminList = empty($adminRoleIds) ? '0' : implode(',', $adminRoleIds);

        $connection = Application::getConnection();
        $helper = $connection->getSqlHelper();
        $entity = $helper->forSql($permissionEntity);

        $connection->queryExecute(
            "DELETE FROM b_crm_role_perms
              WHERE ENTITY = '{$entity}'
                AND ROLE_ID NOT IN ({$adminList})"
        );
    }
}