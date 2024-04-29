<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Applicator;

use FluxSE\SyliusEUVatPlugin\Checker\VatRateEligibilityCheckerInterface;
use FluxSE\SyliusEUVatPlugin\Extractor\ChannelExtractorInterface;
use FluxSE\SyliusEUVatPlugin\Extractor\TaxationAddressExtractorInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;

final class OrderEuropeanVATNumberApplicator implements OrderTaxesApplicatorInterface
{
    public function __construct(
        private TaxationAddressExtractorInterface $taxationAddressExtractor,
        private ChannelExtractorInterface $channelExtractor,
        private VatRateEligibilityCheckerInterface $intraCommunityVATRateEligibilityChecker,
    ) {
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $taxationAddress = $this->taxationAddressExtractor->extract($order);
        if (null === $taxationAddress) {
            return;
        }

        $channel = $this->channelExtractor->extract($order);
        if (null === $channel) {
            return;
        }

        if (false === $this->intraCommunityVATRateEligibilityChecker->check(
            $taxationAddress,
            $channel,
        )) {
            return;
        }

        $order->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
    }
}
