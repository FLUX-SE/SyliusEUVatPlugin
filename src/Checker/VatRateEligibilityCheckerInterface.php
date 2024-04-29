<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Checker;

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;

interface VatRateEligibilityCheckerInterface
{
    public function check(
        AddressInterface $taxationAddress,
        ChannelInterface $channel,
    ): bool;
}
