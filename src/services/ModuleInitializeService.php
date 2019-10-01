<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2ModuleManager\services;

use yii\base\Application;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

class ModuleInitializeService
{
    /** @var Application */
    public $app;

    /**
     * @var array
     */
    private $modulesThatWaitDependencies = [];

    /**
     * @var array
     */
    private $moduleDependencies = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param string|ModuleInterface $class
     * @param callable $config
     */
    public function addModule(string $class, callable $config)
    {
        $requiredModules = $class::requireOtherModulesToBeActive();
        if (empty($requiredModules)) {
            $this->runModule($class, $config);
        } else {
            $this->addToWaitList($class, $config, $requiredModules);
        }
    }

    /**
     * @param string|ModuleInterface $class
     * @param callable $config
     */
    private function runModule($class, callable $config)
    {
        if (!$this->app->hasModule($class::getId())) {
            $this->app->setModule($class::getId(), $config());
            $this->app->getModule($class::getId());
        }
        $this->resolveModulesDependencies($class);
    }

    /**
     * @param string $class
     * @param callable $config
     * @param array $requiredModules
     */
    private function addToWaitList(string $class, callable $config, array $requiredModules): void
    {
        /** @var string $requiredModuleClass */
        foreach ($requiredModules as $requiredModuleClass) {
            if (! array_key_exists($requiredModuleClass, $this->app->loadedModules)) {
                $this->addModuleDependency($class, $requiredModuleClass);
            }
        }
        if ($this->isResolvedModuleDependencies($class)) {
            /** @var ModuleInterface $class */
            $this->runModule($class, $config);
        } else {
            $this->modulesThatWaitDependencies[ $class ] = $config;
        }
    }

    private function addModuleDependency(string $class, string $dependModuleId)
    {
        $this->moduleDependencies[ $class ][] = $dependModuleId;
    }

    private function resolveModulesDependencies(string $dependModuleClass)
    {
        foreach ($this->moduleDependencies as $class => $dependedModules) {
            if (false !== ($key = array_search($dependModuleClass, $dependedModules))) {
                unset($this->moduleDependencies[ $class ][ $key ]);
                if (empty($this->moduleDependencies[ $class ])) {
                    $config = $this->modulesThatWaitDependencies[ $class ];
                    /** @var ModuleInterface $class */
                    $this->runModule($class, $config);
                    unset($this->modulesThatWaitDependencies[ $class ]);
                }
            }
        }
    }

    private function isResolvedModuleDependencies(string $class): bool
    {
        if (empty($this->moduleDependencies[ $class ])) {
            return true;
        } else {
            return false;
        }
    }
}