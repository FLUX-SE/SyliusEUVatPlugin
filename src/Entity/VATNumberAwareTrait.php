<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prometee\VIESClient\Util\VatNumberUtil;

trait VATNumberAwareTrait
{
    /**
     * @ORM\Column(name="vat_number", type="string", nullable=true)
     *
     * @Gedmo\Versioned()
     *
     * @var string|null
     */
    #[ORM\Column(name: 'vat_number', type: 'string', nullable: true)]
    #[Gedmo\Versioned]
    protected ?string $vatNumber = null;

    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    public function setVatNumber(?string $vatNumber): void
    {
        if (null !== $vatNumber) {
            $vatNumber = VatNumberUtil::clean($vatNumber);
        }

        $this->vatNumber = $vatNumber;
    }

    public function hasVatNumber(): bool
    {
        return $this->vatNumber !== null && $this->vatNumber !== '';
    }
}
