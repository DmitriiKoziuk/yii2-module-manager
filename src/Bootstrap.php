<?php
namespace DmitriiKoziuk\yii2ModuleManager;

use Yii;
use yii\base\BootstrapInterface;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleInitService;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        ModuleInitService::registerModule(ModuleManager::class, function () {
            return [
                'class' => ModuleManager::class,
                'diContainer' => Yii::$container,
            ];
        });
    }
}