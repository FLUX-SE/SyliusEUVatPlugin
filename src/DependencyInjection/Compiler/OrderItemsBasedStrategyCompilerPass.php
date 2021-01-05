<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OrderItemsBasedStrategyCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('sylius.taxation.order_items_based_strategy');
        $arg = $definition->getArgument(1);
        $arg[] = new Reference('prometee.sylius_vies_client_plugin.applicator.order_european_vatnumber_applicator');
        $definition->setArgument(1, $arg);
    }
}
