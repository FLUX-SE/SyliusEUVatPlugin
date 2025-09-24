<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Mapper;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Webmozart\Assert\Assert;

final class AddressMapper implements AddressMapperInterface
{
    public function __construct(
        private readonly AddressMapperInterface $decoratedAddressMapper,
    ) {
    }

    public function mapExisting(AddressInterface $currentAddress, AddressInterface $targetAddress): AddressInterface
    {
        $address = $this->decoratedAddressMapper->mapExisting($currentAddress, $targetAddress);
        Assert::isInstanceOf($address, VATNumberAwareInterface::class);
        Assert::isInstanceOf($targetAddress, VATNumberAwareInterface::class);
        $address->setVatNumber($targetAddress->getVatNumber());

        return $address;
    }
}
