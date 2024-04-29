<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Checker;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class DefaultEligibilityChecker implements VatRateEligibilityCheckerInterface
{
    public function __construct(
        /**
         * @var iterable|VatRateEligibilityCheckerInterface[]
         */
        private iterable $checkers,
    ) {
    }

    public function check(
        AddressInterface&VATNumberAwareInterface $taxationAddress,
        ChannelInterface&EuropeanChannelAwareInterface $channel,
    ): bool {
        /** @var VatRateEligibilityCheckerInterface $checker */
        foreach ($this->checkers as $checker) {
            if (false === $checker->check($taxationAddress, $channel)) {
                return false;
            }
        }

        return true;
    }
}
