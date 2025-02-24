<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Matcher;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;

interface CountryCodeZoneMatcherInterface
{
    public function countryCodeBelongsToZoneMember(string $countryCode, ZoneMemberInterface $member): bool;

    public function countryCodeBelongsToZone(string $countryCode, ZoneInterface $zone): bool;
}
