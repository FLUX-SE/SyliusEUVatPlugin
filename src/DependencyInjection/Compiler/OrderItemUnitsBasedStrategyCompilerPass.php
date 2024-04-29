<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Argument\ArgumentInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class OrderItemUnitsBasedStrategyCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('sylius.taxation.order_item_units_based_strategy');

        /** @var array|ArgumentInterface $applicators */
        $applicators = $definition->getArgument(1);
        // Sylius >= 1.13
        if ($applicators instanceof ArgumentInterface) {
            return;
        }
        $applicators[] = new Reference('flux_se.sylius_eu_vat_plugin.applicator.order_european_vat_number');
        $definition->setArgument(1, $applicators);
    }
}
