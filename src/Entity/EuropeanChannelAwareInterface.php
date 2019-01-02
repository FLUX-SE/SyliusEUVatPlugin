<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

interface EuropeanChannelAwareInterface
{
    /**
     * @return CountryInterface|null
     */
    public function getBaseCountry(): ?CountryInterface;

    /**
     * @param ZoneInterface|null $europeanZone
     */
    public function setEuropeanZone(?ZoneInterface $europeanZone): void;

    /**
     * @return ZoneInterface|null
     */
    public function getEuropeanZone(): ?ZoneInterface;

    /**
     * @param CountryInterface|null $baseCountry
     */
    public function setBaseCountry(?CountryInterface $baseCountry): void;
}
