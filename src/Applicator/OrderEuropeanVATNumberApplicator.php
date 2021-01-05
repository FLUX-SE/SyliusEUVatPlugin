<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Applicator;

use InvalidArgumentException;
use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Webmozart\Assert\Assert;

final class OrderEuropeanVATNumberApplicator implements OrderTaxesApplicatorInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        $billingVatNumber = $this->extractSupportedBillingVatNumber($order);
        if (null === $billingVatNumber) {
            return;
        }

        $billingCountryCode = $this->extractSupportedBillingCountryCode($order);
        if (null === $billingCountryCode) {
            return;
        }

        $channel = $this->extractSupportedChannel($order->getChannel());
        if (null === $channel) {
            return;
        }

        if (false === $this->isValidForZeroEuropeanVAT(
            $billingVatNumber,
            $billingCountryCode,
            $zone,
            $channel
        )) {
            return;
        }

        foreach ($order->getItems() as $item) {
            $quantity = $item->getQuantity();
            Assert::notSame($quantity, 0, 'Cannot apply tax to order item with 0 quantity.');

            $item->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
        }
    }

    private function isValidForZeroEuropeanVAT(
        string $billingVatNumber,
        string $billingCountryCode,
        ZoneInterface $zone,
        EuropeanChannelAwareInterface $channel
    ): bool {
        if (true === $this->isChannelSameCountryCode($channel, $billingCountryCode)) {
            return false;
        }

        if ($zone !== $channel->getEuropeanZone()) {
            return false;
        }

        $vatNumberArr = VatNumberUtil::split($billingVatNumber);
        $vatCountryCode = null === $vatNumberArr ? null : $vatNumberArr[0];

        if ($billingCountryCode !== $vatCountryCode) {
            return false;
        }

        return true;
    }

    private function extractSupportedBillingVatNumber(OrderInterface $order): ?string
    {
        $billingAddress = $this->extractSupportedBillingAddress($order);

        if (null === $billingAddress) {
            return null;
        }

        return $billingAddress->getVatNumber();
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

    private function extractSupportedBillingAddress(OrderInterface $order): ?VATNumberAwareInterface
    {
        $billingAddress = $order->getBillingAddress();

        if (null === $billingAddress) {
            return null;
        }

        if (false === $billingAddress instanceof VATNumberAwareInterface) {
            return null;
        }

        return $billingAddress;
    }

    private function extractSupportedBillingCountryCode(OrderInterface $order): ?string
    {
        $billingAddress = $this->extractSupportedBillingAddress($order);

        if (null === $billingAddress) {
            return null;
        }

        if (false === $billingAddress instanceof AddressInterface) {
            return null;
        }

        return $billingAddress->getCountryCode();
    }

    private function isChannelSameCountryCode(
        EuropeanChannelAwareInterface $channel,
        string $billingCountryCode
    ): bool {
        $baseCountry = $channel->getBaseCountry();
        if (null === $baseCountry) {
            return false;
        }

        if ($billingCountryCode !== $baseCountry->getCode()) {
            return false;
        }

        return true;
    }
}
