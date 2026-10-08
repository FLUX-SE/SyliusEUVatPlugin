<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $container): void {
    $env = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'dev';

    if (str_starts_with($env, 'test')) {
        $services = '../../../vendor/sylius/sylius/src/Sylius/Behat/Resources/config/services';
        $container->import($services . (is_file(__DIR__ . '/' . $services . '.php') ? '.php' : '.xml'));
        $container->import('../../../tests/Behat/Resources/services.yaml');
    }
};
