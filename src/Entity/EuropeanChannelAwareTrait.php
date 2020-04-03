<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

trait EuropeanChannelAwareTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Addressing\Model\CountryInterface", fetch="EAGER")
     * @ORM\JoinColumn(name="base_country_id", onDelete="SET NULL")
     *
     * @var CountryInterface|null
     */
    protected $baseCountry;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Addressing\Model\ZoneInterface", fetch="EAGER")
     * @ORM\JoinColumn(name="european_zone_id", onDelete="SET NULL")
     *
     * @var ZoneInterface|null
     */
    protected $europeanZone;

    public function getBaseCountry(): ?CountryInterface
    {
        return $this->baseCountry;
    }

    public function setBaseCountry(?CountryInterface $baseCountry): void
    {
        $this->baseCountry = $baseCountry;
    }

    public function getEuropeanZone(): ?ZoneInterface
    {
        return $this->europeanZone;
    }

    public function setEuropeanZone(?ZoneInterface $europeanZone): void
    {
        $this->europeanZone = $europeanZone;
    }
}
