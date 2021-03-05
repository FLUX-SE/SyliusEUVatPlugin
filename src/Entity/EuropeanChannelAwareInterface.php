<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Entity;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

interface EuropeanChannelAwareInterface
{
    public function getBaseCountry(): ?CountryInterface;

    public function setEuropeanZone(?ZoneInterface $europeanZone): void;

    public function getEuropeanZone(): ?ZoneInterface;

    public function setBaseCountry(?CountryInterface $baseCountry): void;
}
