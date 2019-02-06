<?php
namespace DmitriiKoziuk\yii2ModuleManager\data;

use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

class ModuleData
{
    /** @var ModuleInterface */
    private $_module;
    private $_runFunction;

    public function __construct($module, callable $runFunction)
    {
        $this->_module = $module;
        $this->_runFunction = $runFunction;
    }

    public function getId(): string
    {
        return $this->_module::getId();
    }

    public function getRequiredModules(): array
    {
        return $this->_module::getRequiredModules();
    }

    public function runModule(): void
    {

    }
}