<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class EuropeanChannelFixture extends AbstractResourceFixture
{
    public function getName(): string
    {
        return 'eu_vat_plugin_european_channel';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        $resourceNode
            ->children()
                ->scalarNode('channel')->cannotBeEmpty()->end()
                ->scalarNode('base_country')->cannotBeEmpty()->end()
                ->scalarNode('european_zone')->cannotBeEmpty()->end();
    }
}
