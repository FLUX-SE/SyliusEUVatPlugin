<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

trait EuropeanChannelAwareTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Addressing\Model\CountryInterface", fetch="EAGER")
     *
     * @ORM\JoinColumn(name="base_country_id", onDelete="SET NULL")
     *
     * @var CountryInterface|null
     */
    #[ORM\JoinColumn(name: 'base_country_id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: CountryInterface::class, fetch: 'EAGER')]
    protected ?CountryInterface $baseCountry = null;

    /**
     * @ORM\ManyToOne(targetEntity="Sylius\Component\Addressing\Model\ZoneInterface", fetch="EAGER")
     *
     * @ORM\JoinColumn(name="european_zone_id", onDelete="SET NULL")
     *
     * @var ZoneInterface|null
     */
    #[ORM\JoinColumn(name: 'european_zone_id', onDelete: 'SET NULL')]
    #[ORM\ManyToOne(targetEntity: ZoneInterface::class, fetch: 'EAGER')]
    protected ?ZoneInterface $europeanZone = null;

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
