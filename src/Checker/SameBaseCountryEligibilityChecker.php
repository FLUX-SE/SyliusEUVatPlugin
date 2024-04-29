<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Checker;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class SameBaseCountryEligibilityChecker implements VatRateEligibilityCheckerInterface
{
    public function check(
        AddressInterface&VATNumberAwareInterface $taxationAddress,
        ChannelInterface&EuropeanChannelAwareInterface $channel,
    ): bool {
        $taxationCountryCode = $taxationAddress->getCountryCode();
        if (null === $taxationCountryCode) {
            return false;
        }

        if (true === $this->isBaseCountrySameAsCountryCode($channel, $taxationCountryCode)) {
            return false;
        }

        return true;
    }

    private function isBaseCountrySameAsCountryCode(
        EuropeanChannelAwareInterface $channel,
        string $taxationCountryCode,
    ): bool {
        $baseCountry = $channel->getBaseCountry();
        if (null === $baseCountry) {
            return false;
        }

        return $taxationCountryCode === $baseCountry->getCode();
    }
}
