<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Extractor;

use FluxSE\SyliusEUVatPlugin\Applicator\OrderEuropeanVATNumberApplicator;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;

final class TaxationAddressExtractor implements TaxationAddressExtractorInterface
{
    public function __construct(
        private TaxationAddressResolverInterface $taxationAddressResolver,
    ) {
    }

    public function extract(OrderInterface $order): ?VATNumberAwareInterface
    {
        $taxationAddress = $this->taxationAddressResolver->getTaxationAddressFromOrder($order);

        if (null === $taxationAddress) {
            return null;
        }

        return $taxationAddress instanceof VATNumberAwareInterface ? $taxationAddress : null;
    }
}
