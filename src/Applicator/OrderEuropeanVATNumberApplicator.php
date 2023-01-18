<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Applicator;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Resolver\TaxationAddressResolverInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;

final class OrderEuropeanVATNumberApplicator implements OrderTaxesApplicatorInterface
{
    public function __construct(
        private ZoneMatcherInterface $zoneMatcher,
        private TaxationAddressResolverInterface $taxationAddressResolver
    ) {
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $taxationAddress = $this->extractSupportedTaxationAddress($order);
        if (null === $taxationAddress) {
            return;
        }

        $channel = $order->getChannel();
        if (false === $channel instanceof EuropeanChannelAwareInterface) {
            return;
        }

        $channel = $this->extractSupportedChannel($channel);
        if (null === $channel) {
            return;
        }

        if (false === $this->isValidForZeroEuropeanVAT(
            $taxationAddress,
            $channel
        )) {
            return;
        }

        $order->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
    }

    private function isValidForZeroEuropeanVAT(
        VATNumberAwareInterface $billingAddress,
        EuropeanChannelAwareInterface $channel
    ): bool {
        if (false === $billingAddress instanceof AddressInterface) {
            return false;
        }

        $billingCountryCode = $billingAddress->getCountryCode();
        if (null === $billingCountryCode) {
            return false;
        }

        if (true === $this->isBaseCountrySameAsCountryCode($channel, $billingCountryCode)) {
            return false;
        }

        $channelEUZone = $channel->getEuropeanZone();
        if (null === $channelEUZone) {
            return false;
        }

        if (false === $this->addressBelongsToEUZone($billingAddress, $channelEUZone)) {
            return false;
        }

        $billingVatNumber = $billingAddress->getVatNumber();
        if (null === $billingVatNumber) {
            return false;
        }

        $vatNumberArr = VatNumberUtil::split($billingVatNumber);
        $vatCountryCode = null === $vatNumberArr ? null : $vatNumberArr[0];

        // Greece country ISO code is not 'GR'
        if ('GR' === $billingCountryCode && 'EL' === $vatCountryCode) {
            return true;
        }

        // Northern Ireland exception
        if ('GB' === $billingCountryCode && 'XI' === $vatCountryCode) {
            return true;
        }

        return $billingCountryCode === $vatCountryCode;
    }

    private function extractSupportedChannel(?ChannelInterface $channel): ?EuropeanChannelAwareInterface
    {
        if (null === $channel) {
            return null;
        }

        if (false === $channel instanceof EuropeanChannelAwareInterface) {
            return null;
        }

        if (null === $channel->getBaseCountry()) {
            return null;
        }

        if (null === $channel->getEuropeanZone()) {
            return null;
        }

        return $channel;
    }

    private function extractSupportedTaxationAddress(OrderInterface $order): ?VATNumberAwareInterface
    {
        $taxationAddress = $this->taxationAddressResolver->getTaxationAddressFromOrder($order);

        if (null === $taxationAddress) {
            return null;
        }

        return $taxationAddress instanceof VATNumberAwareInterface ? $taxationAddress : null;
    }

    private function isBaseCountrySameAsCountryCode(
        EuropeanChannelAwareInterface $channel,
        string $billingCountryCode
    ): bool {
        $baseCountry = $channel->getBaseCountry();
        if (null === $baseCountry) {
            return false;
        }

        return $billingCountryCode === $baseCountry->getCode();
    }

    private function addressBelongsToEUZone(AddressInterface $address, ZoneInterface $channelEUZone): bool
    {
        $matchedZones = $this->zoneMatcher->matchAll($address, Scope::ALL);

        $belongsToEUZone = false;
        /** @var ZoneInterface $matchedZone */
        foreach ($matchedZones as $matchedZone) {
            if ($matchedZone === $channelEUZone) {
                $belongsToEUZone = true;
            }
        }

        return $belongsToEUZone;
    }
}
