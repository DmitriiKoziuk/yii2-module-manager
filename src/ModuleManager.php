<?php
namespace DmitriiKoziuk\yii2ModuleManager;

use yii\base\Event;
use yii\bootstrap\Nav;
use yii\web\Application as WebApp;
use yii\base\Application as BaseApp;
use DmitriiKoziuk\yii2ModuleManager\interfaces\ModuleInterface;
use DmitriiKoziuk\yii2ModuleManager\services\ModuleService;

final class ModuleManager extends \yii\base\Module implements ModuleInterface
{
    const ID = 'dk-module-manager';

    const TRANSLATE = self::ID;

    /**
     * @var \yii\di\Container
     */
    public $diContainer;

    /**
     * Overwrite this param if you backend app id is different from default.
     * @var string
     */
    public $backendAppId = 'app-backend';

    public function init()
    {
        /** @var BaseApp $app */
        $app = $this->module;
        $this->_registerTranslation($app);
        $this->_registerClassesToDIContainer($app);
        $this->_subscribeToEvents($app);
    }

    public static function getId(): string
    {
        return self::ID;
    }

    public function getBackendMenuItems(): array
    {
        return [];
    }

    public static function requireOtherModulesToBeActive(): array
    {
        return [];
    }

    private function _registerTranslation($app)
    {
        $app->i18n->translations[self::TRANSLATE] = [
            'class'          => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en',
            'basePath'       => '@DmitriiKoziuk/yii2ModuleManager/messages',
        ];
    }

    private function _registerClassesToDIContainer($app)
    {
        $this->diContainer->setSingleton(ModuleService::class, function () use ($app) {
            return new ModuleService($app);
        });
    }

    private function _subscribeToEvents($app)
    {
        if ($app instanceof WebApp && $app->id == $this->backendAppId && ! $app->getUser()->isGuest) {
            Event::on(Nav::class, Nav::EVENT_INIT, function ($event) {
                /** @var ModuleService $moduleService */
                $moduleService = $this->diContainer->get(ModuleService::class);
                /** @var Nav $nav */
                $nav = $event->sender;
                array_unshift($nav->items, $moduleService->getModulesMenuItems());
            });
        }
    }
}