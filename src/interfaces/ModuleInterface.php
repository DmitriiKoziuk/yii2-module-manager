<?php
namespace DmitriiKoziuk\yii2ModuleManager\interfaces;

interface ModuleInterface
{
    public function getId(): string;
    public function getBackendMenuItems(): array;
}