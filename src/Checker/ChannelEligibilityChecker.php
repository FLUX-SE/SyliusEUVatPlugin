<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Checker;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\Scope;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;

final class ChannelEligibilityChecker implements VatRateEligibilityCheckerInterface
{

    public function __construct(
        private ZoneMatcherInterface $zoneMatcher,
    ) {
    }

    public function check(
        VATNumberAwareInterface&AddressInterface $taxationAddress,
        ChannelInterface&EuropeanChannelAwareInterface $channel,
    ): bool {
        $channelEUZone = $channel->getEuropeanZone();
        if (null === $channelEUZone) {
            return false;
        }

        if (false === $this->addressBelongsToEUZone($taxationAddress, $channelEUZone)) {
            return false;
        }

        return true;
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
