<?php

namespace Base\Module\Service\Migration\SmartProcess;

interface ClosePermissionsOnCategoriesService
{
    public const SERVICE_CODE = 'base.module.migration.smart.process.close.permissions.service';

    /**
     * Закрывает доступ к воронкам смарт-процесса, выставляя нулевые права всем ролям.
     *
     * Вызывается после создания типа смарт-процесса, когда параметр
     * «Права доступа при создании воронки» (IS_SET_OPEN_PERMISSIONS) выключен (N),
     * чтобы у всех ролей (включая администраторские) не осталось доступа к воронке.
     * Удаляются все записи прав по каждой воронке — отсутствие записей и есть
     * «нулевые права».
     *
     * Используется штатный API ядра CCrmRole::EraseEntityPermissons().
     *
     * @param int $entityTypeId Entity type id смарт-процесса.
     * @return void
     */
    public function closePermissions(int $entityTypeId): void;
}