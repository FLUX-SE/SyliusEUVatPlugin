<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Modifier;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Bundle\ShopBundle\Modifier\AddressFormValuesModifierInterface;
use Sylius\Component\Addressing\Model\AddressInterface;

final readonly class VATNumberAddressFormValuesModifier implements AddressFormValuesModifierInterface
{
    /**
     * @param array<string, mixed> $addressData
     *
     * @return array<string, mixed>
     */
    public function modify(array $addressData, AddressInterface $address): array
    {
        if (!$address instanceof VATNumberAwareInterface) {
            return $addressData;
        }

        $addressData['vatNumber'] = $address->getVatNumber();

        return $addressData;
    }
}
