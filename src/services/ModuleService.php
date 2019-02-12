<?php
namespace DmitriiKoziuk\yii2ModuleManager\services;

use yii\base\Application;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

class ModuleService
{
    /**
     * @var Application
     */
    public $_app;

    public function __construct(Application $app)
    {
        $this->_app = $app;
    }

    public function getModulesMenuItems(): array
    {
        $items = [];
        foreach ($this->_app->loadedModules as $module) {
            if (
                $module instanceof ModuleInterface &&
                ! empty($module->getBackendMenuItems())
            ) {
                $moduleItems = $module->getBackendMenuItems();
                $items[ $moduleItems['label'] ] = $moduleItems;
            }
        }
        ksort($items);
        return $menu = ['label' => 'Modules', 'items' => $items];
    }
}