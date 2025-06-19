<?php

namespace Base\Module\Src\Migration\SmartProcess;

class ParamsConstructor
{
    private array $params = [];

    public function getParamsInArray(): array
    {
        return $this->params;
    }
    
    /**
     * @return $this
     * Основные настройки -> Работа с воронками и туннелями продаж
     */
    public function setIsCategoriesEnabled(bool $value = true): self
    {
        $this->params['IS_CATEGORIES_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Основные настройки -> Стадии и канбан
     */
    public function setIsStagesEnabled(bool $value = true): self
    {
        $this->params['IS_STAGES_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Основные настройки -> Использовать в смарт-процессе роботы и триггеры
     */
    public function setIsOnUseRobotsAndTriggers(bool $value = true): self
    {
        $this->params['IS_AUTOMATION_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }


    /**
     * @return $this
     * Основные настройки -> Права доступа при создании воронки
     */
    public function setIsOpenPermissions(bool $value = true): self
    {
        $this->params['IS_SET_OPEN_PERMISSIONS'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Вид Карточки -> Поля в карточке смарт-процесса -> Реквизиты компании
     */
    public function setIsMyCompanyEnabled(bool $value = true): self
    {
        $this->params['IS_MYCOMPANY_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Вид Карточки -> Поля в карточке смарт-процесса -> Клиент
     */
    public function setIsClientEnabled(bool $value = true): self
    {
        $this->params['IS_CLIENT_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Вид Карточки -> Дополнительные возможности -> Использовать корзину
     */
    public function setIsRecycleBinEnabled(bool $value = true): self
    {
        $this->params['IS_RECYCLEBIN_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Вид Карточки -> Дополнительные возможности -> Использовать Счетчики
     */
    public function setIsCountersEnabled(bool $value = true): self
    {
        $this->params['IS_COUNTERS_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }

    /**
     * @return $this
     * Привязка к элементам -> Элементы CRM
     */
    public function setIsUseInUserfieldEnabled(bool $value = true): self
    {
        $this->params['IS_USE_IN_USERFIELD_ENABLED'] = $value ? 'Y' : 'N';
        return $this;
    }
}