<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Checker;

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
        AddressInterface $taxationAddress,
        ChannelInterface $channel,
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
