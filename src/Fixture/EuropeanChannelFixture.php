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
        $node = $resourceNode->children();
        $node->scalarNode('channel')->cannotBeEmpty();
        $node->scalarNode('base_country')->cannotBeEmpty();
        $node->scalarNode('european_zone')->cannotBeEmpty();
    }
}
