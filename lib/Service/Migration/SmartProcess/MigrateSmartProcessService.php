<?php

namespace Base\Module\Service\Migration\SmartProcess;

interface MigrateSmartProcessService
{
    public const SERVICE_CODE = 'base.module.migration.smart.process.service';

    /**
     * @param MigrateSmartProcessEntity[] $smartProcessList
     * @return self
     */
    public function setSmartProcessList(array $smartProcessList): self;
    public function install(): void;
    public function reInstall(): void;
    public function getParamsConstructor(): object;

    /**
     * @return array<int, array{
     *     name: string,
     *     title: string,
     *     code: string,
     *     exists: bool,
     *     id: string,
     *     entityTypeId: string,
     * }>
     */
    public function getSmartProcessStatus(): array;
}