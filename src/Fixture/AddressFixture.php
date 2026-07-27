<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Fixture;

use Sylius\Bundle\CoreBundle\Fixture\AddressFixture as BaseAddressFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class AddressFixture extends BaseAddressFixture
{
    public function getName(): string
    {
        return 'address_with_vat_number';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        parent::configureResourceNode($resourceNode);

        $resourceNode
            ->children()
                ->scalarNode('vat_number')->end();
    }
}
