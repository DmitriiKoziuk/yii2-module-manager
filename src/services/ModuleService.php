<?php
namespace DmitriiKoziuk\yii2ModuleManager\services;

use yii\base\Application;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

class ModuleService
{
    private $_app;

    /**
     * @var ModuleInterface[]
     */
    private $_modules = [];

    public function __construct(Application $app)
    {
        $this->_app = $app;
    }

    public function registerModule(ModuleInterface $module)
    {
        $this->_modules[ $module->getId() ] = $module;
    }

    /**
     * @return ModuleInterface[]
     */
    public function getModules(): array
    {
        return $this->_modules;
    }

    public function getModulesMenuItems(): array
    {
        $menuItems = ['label' => 'Modules', 'items' => []];
        foreach ($this->_modules as $module) {
            $menuItems['items'][] = $module->getBackendMenuItems();
        }
        return $menuItems;
    }
}