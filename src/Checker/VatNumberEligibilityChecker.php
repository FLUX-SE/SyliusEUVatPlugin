<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Checker;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class VatNumberEligibilityChecker implements VatRateEligibilityCheckerInterface
{
    public function check(
        AddressInterface&VATNumberAwareInterface $taxationAddress,
        ChannelInterface&EuropeanChannelAwareInterface $channel,
    ): bool {
        $taxationCountryCode = $taxationAddress->getCountryCode();
        if (null === $taxationCountryCode) {
            return false;
        }

        $taxationVatNumber = $taxationAddress->getVatNumber();
        if (null === $taxationVatNumber) {
            return false;
        }

        $vatNumberArr = VatNumberUtil::split($taxationVatNumber);
        $vatCountryCode = null === $vatNumberArr ? null : $vatNumberArr[0];

        // Greece country ISO code is not 'GR'
        if ('GR' === $taxationCountryCode && 'EL' === $vatCountryCode) {
            return true;
        }
        // Greece with not possible vat country code
        if ('GR' === $taxationCountryCode && 'GR' === $vatCountryCode) {
            return false;
        }

        // GB is out of Europe exception
        if ('GB' === $taxationCountryCode && 'GB' === $vatCountryCode) {
            return false;
        }

        // Northern Ireland exception
        if ('GB' === $taxationCountryCode && 'XI' === $vatCountryCode) {
            return true;
        }

        return $taxationCountryCode === $vatCountryCode;
    }
}
