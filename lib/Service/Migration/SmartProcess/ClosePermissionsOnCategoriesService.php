<?php

namespace Base\Module\Service\Migration\SmartProcess;

interface ClosePermissionsOnCategoriesService
{
    public const SERVICE_CODE = 'base.module.migration.smart.process.close.permissions.service';

    /**
     * Закрывает доступ к воронкам смарт-процесса для всех ролей, кроме администраторских.
     *
     * Вызывается после создания типа смарт-процесса, когда параметр
     * «Права доступа при создании воронки» (IS_SET_OPEN_PERMISSIONS) выключен (N),
     * чтобы у ролей с пресетами (MANAGER/DEPUTY/HEAD/OBSERVER) тоже не осталось доступа,
     * который им автоматически выдаёт ядро Bitrix при создании воронки.
     *
     * @param int $entityTypeId Entity type id смарт-процесса.
     * @return void
     */
    public function closeForNotAdminRoles(int $entityTypeId): void;
}