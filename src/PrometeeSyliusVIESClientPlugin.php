<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin;

use Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler\OrderItemsBasedStrategyCompilerPass;
use Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler\OrderItemUnitsBasedStrategyCompilerPass;
use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PrometeeSyliusVIESClientPlugin extends Bundle
{
    use SyliusPluginTrait;

    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new OrderItemsBasedStrategyCompilerPass());
        $container->addCompilerPass(new OrderItemUnitsBasedStrategyCompilerPass());

        parent::build($container);
    }
}
