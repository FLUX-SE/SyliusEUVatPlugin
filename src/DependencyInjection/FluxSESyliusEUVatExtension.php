<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\DependencyInjection;

use Sylius\Bundle\CoreBundle\DependencyInjection\PrependDoctrineMigrationsTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class FluxSESyliusEUVatExtension extends Extension implements PrependExtensionInterface
{
    use PrependDoctrineMigrationsTrait;

    protected function getMigrationsNamespace(): string
    {
        return 'FluxSE\SyliusEUVatPlugin\Migrations';
    }

    protected function getMigrationsDirectory(): string
    {
        return '@FluxSESyliusEUVatPlugin/Migrations';
    }

    protected function getNamespacesOfMigrationsExecutedBefore(): array
    {
        return [
            'Sylius\Bundle\CoreBundle\Migrations',
        ];
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config'),
        );

        $loader->load('services.yaml');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);
    }

    public function getAlias(): string
    {
        return 'flux_se_sylius_eu_vat';
    }
}
