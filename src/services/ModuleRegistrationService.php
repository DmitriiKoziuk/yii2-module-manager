<?php
namespace DmitriiKoziuk\yii2ModuleManager\services;

use Yii;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;

class ModuleRegistrationService
{
    /**
     * @param string|ModuleInterface $class
     * @param callable $config
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public static function addModule(string $class, callable $config): void
    {
        $moduleInitService = self::_init();
        $moduleInitService->addModule($class, $config);
    }

    /**
     * @return ModuleInitializeService
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private static function _init(): ModuleInitializeService
    {
        $container = Yii::$container;
        if (!$container->hasSingleton(ModuleInitializeService::class)) {
            $container->setSingleton(ModuleInitializeService::class, function () {
                return new ModuleInitializeService(
                    Yii::$app
                );
            });
        }
        return $container->get(ModuleInitializeService::class);
    }
}