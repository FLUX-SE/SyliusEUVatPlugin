<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Extractor;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface TaxationAddressExtractorInterface
{
    /**
     * @return (AddressInterface&VATNumberAwareInterface)|null
     */
    public function extract(OrderInterface $order): ?VATNumberAwareInterface;
}
