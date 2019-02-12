<?php
namespace DmitriiKoziuk\yii2ModuleManager\interfaces;

interface ModuleInterface
{
    /**
     * @return string
     */
    public static function getId(): string;

    /**
     * @return array
     */
    public function getBackendMenuItems(): array;

    /**
     * @return \yii\base\Module[]
     */
    public static function requireOtherModulesToBeActive(): array;
}