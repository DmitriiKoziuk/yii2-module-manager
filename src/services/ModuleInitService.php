<?php
namespace DmitriiKoziuk\yii2ModuleManager\services;

use yii\base\Application;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

class ModuleInitService
{
    /**
     * @var Application
     */
    private static $_app;

    /**
     * @var array
     */
    private static $_modulesThatWaitDependencies = [];

    /**
     * @var array
     */
    private static $_moduleDependencies = [];

    /**
     * @param string $class
     * @param callable $config
     */
    public static function registerModule(string $class, callable $config)
    {
        self::_init();
        /** @var ModuleInterface $class */
        $requireOtherModules = $class::requireOtherModulesToBeActive();
        if (empty($requireOtherModules)) {
            self::_runModule($class, $config);
        } else {
            self::_addToWaiting($class, $config, $requireOtherModules);
        }
    }

    private static function _init()
    {
        if (empty(self::$_app)) {
            self::$_app = \Yii::$app;
        }
    }

    /**
     * @param ModuleInterface $class
     * @param callable $config
     */
    private static function _runModule($class, callable $config)
    {
        self::$_app->setModule($class::getId(), $config());
        self::$_app->getModule($class::getId());
        self::_resolveModulesDependencies($class);
    }

    /**
     * @param string $class
     * @param callable $config
     * @param array $requiredModules
     */
    private static function _addToWaiting(string $class, callable $config, array $requiredModules): void
    {
        /** @var ModuleInterface $requiredModuleClass */
        foreach ($requiredModules as $requiredModuleClass) {
            if (! array_key_exists($requiredModuleClass, self::$_app->loadedModules)) {
                self::_addModuleDependency($class, $requiredModuleClass);
            }
        }
        if (self::_isResolvedModuleDependencies($class)) {
            /** @var ModuleInterface $class */
            self::_runModule($class, $config);
        } else {
            self::$_modulesThatWaitDependencies[ $class ] = $config;
        }
    }

    private static function _addModuleDependency(string $class, string $dependModuleId)
    {
        self::$_moduleDependencies[ $class ][] = $dependModuleId;
    }

    private static function _resolveModulesDependencies(string $dependModuleClass)
    {
        foreach (self::$_moduleDependencies as $class => $dependedModules) {
            if (false !== ($key = array_search($dependModuleClass, $dependedModules))) {
                unset(self::$_moduleDependencies[ $class ][ $key ]);
                if (empty(self::$_moduleDependencies[ $class ])) {
                    $config = self::$_modulesThatWaitDependencies[ $class ];
                    /** @var ModuleInterface $class */
                    self::_runModule($class, $config);
                    unset(self::$_modulesThatWaitDependencies[ $class ]);
                }
            }
        }
    }

    private static function _isResolvedModuleDependencies(string $class): bool
    {
        if (empty(self::$_moduleDependencies[ $class ])) {
            return true;
        } else {
            return false;
        }
    }
}