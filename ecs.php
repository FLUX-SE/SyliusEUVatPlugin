<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__.'/vendor/symplify/easy-coding-standard/config/set/symfony.php');

    $services = $containerConfigurator->services();

    /**
     * Miss configured fixer into sylius-labs/coding-standard v4.1.0
     */
    $services->set(ClassAttributesSeparationFixer::class);
};