<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin;

use FluxSE\SyliusEUVatPlugin\DependencyInjection\Compiler\OrderItemsBasedStrategyCompilerPass;
use FluxSE\SyliusEUVatPlugin\DependencyInjection\Compiler\OrderItemUnitsBasedStrategyCompilerPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FluxSESyliusEUVatPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new OrderItemsBasedStrategyCompilerPass());
        $container->addCompilerPass(new OrderItemUnitsBasedStrategyCompilerPass());

        parent::build($container);
    }
}
