# base.module.migration.smart.process

<table>
<tr>
<td>
<a href="https://github.com/Liventin/base.module">Bitrix Base Module</a>
</td>
</tr>
</table>

install | update

```
"require": {
    "liventin/base.module.migration.smart.process": "^1.0.0"
}
```
redirect (optional)
```
"extra": {
  "service-redirect": {
    "liventin/base.module.migration.smart.process": "module.name",
  }
}
```
PhpStorm Live Template
```php
<?php

namespace ${MODULE_PROVIDER_CAMMAL_CASE}\\${MODULE_CODE_CAMMAL_CASE}\Migration\SmartProcess;


use ${MODULE_PROVIDER_CAMMAL_CASE}\\${MODULE_CODE_CAMMAL_CASE}\Service\Container;
use ${MODULE_PROVIDER_CAMMAL_CASE}\\${MODULE_CODE_CAMMAL_CASE}\Service\Migration\SmartProcess\MigrateSmartProcessEntity;
use ${MODULE_PROVIDER_CAMMAL_CASE}\\${MODULE_CODE_CAMMAL_CASE}\Service\Migration\SmartProcess\MigrateSmartProcessService;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\SystemException;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;

class SmartProcessExample implements MigrateSmartProcessEntity
{
    public static function getName(): string
    {
        return 'ExampleName';
    }

    public static function getTitle(): string
    {
        return 'ExampleTitle';
    }

    public static function getCode(): string
    {
        return 'EXAMPLE_CODE';
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ObjectNotFoundException
     * @throws ReflectionException
     * @throws SystemException
     */
    public static function getParams(): array
    {
        /** @var MigrateSmartProcessService ${DS}service */
        ${DS}service = Container::get(MigrateSmartProcessService::SERVICE_CODE);

        /** @var ParamsConstructor ${DS}config */
        ${DS}config = ${DS}service->getParamsConstructor();

        return ${DS}config
            ->setIsOpenPermissions(false)
            ->getParamsInArray();
    }
}
```

## Права доступа при создании воронки (`IS_SET_OPEN_PERMISSIONS`)

Параметр «Права доступа при создании воронки» задаётся через `ParamsConstructor::setIsOpenPermissions(bool)`.

- `false` (`'N'`) — после создания смарт-процесса **доступ к его воронкам закрывается для всех ролей, кроме администраторских**. Это перекрывает дефолтное поведение ядра Bitrix, которое при создании воронки автоматически выдаёт права ролям по их пресетам (MANAGER → «свои», DEPUTY → «отдел», HEAD → «все»).
- `true` (`'Y'`) — поведение ядра не меняется: права выдаются как обычно.

Закрытие выполняется **фоновой задачей** (поставленной в очередь ПОСЛЕ задачи ядра, которая выдаёт права), чтобы гарантированно перекрыть выдачу прав ядром. Используется штатный API ядра `CategoryPermissionsManager::setPermissions()` с уровнем `PERMISSION_NONE` — он сам пропускает администраторские и системные роли и пишет права через ORM. Перед вызовом сбрасывается ORM-кэш ролей (`RolePermissionTable::cleanCache()`), чтобы определение админов читало свежие данные и админы гарантированно сохраняли доступ.