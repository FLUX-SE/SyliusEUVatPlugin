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
        return '@FluxSESyliusEUVatPlugin/migrations';
    }

    /**
     * @return string[]
     */
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
            new FileLocator(__DIR__ . '/../../config'),
        );

        $loader->load('services.yaml');

        if ($container->hasParameter('kernel.bundles')) {
            /** @var string[] $bundles */
            $bundles = $container->getParameter('kernel.bundles');
            if (array_key_exists('SyliusShopBundle', $bundles)) {
                $loader->load('services/integrations/sylius_shop.yaml');
            }
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $this->prependDoctrineMigrations($container);

        if ($container->hasExtension('sylius_api')) {
            $this->prependApiPlatformMapping($container);
        }
    }

    private function prependApiPlatformMapping(ContainerBuilder $container): void
    {
        /** @var array<string, array<string, string>> $metadata */
        $metadata = $container->getParameter('kernel.bundles_metadata');

        $path = $metadata['FluxSESyliusEUVatPlugin']['path'] . '/config/api_resources';

        $container->prependExtensionConfig('api_platform', ['mapping' => ['paths' => [$path]]]);
    }
}
