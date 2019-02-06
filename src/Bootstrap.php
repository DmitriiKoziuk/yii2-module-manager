<?php
namespace DmitriiKoziuk\yii2ModuleManager;

use Yii;
use yii\base\BootstrapInterface;

final class Bootstrap implements BootstrapInterface
{
    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app)
    {
        $app->setModule(ModuleManager::ID, [
            'class' => ModuleManager::class,
            'diContainer' => Yii::$container,
        ]);
        $app->getModule(ModuleManager::ID);
    }
}