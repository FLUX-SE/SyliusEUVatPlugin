<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Prometee\VIESClient\Util\VatNumberUtil;

trait VATNumberAwareTrait
{
    /**
     * @ORM\Column(name="vat_number", type="string", nullable=true)
     * @Gedmo\Versioned()
     *
     * @var string|null
     */
    protected $vatNumber;

    /**
     * {@inheritdoc}
     */
    public function getVatNumber(): ?string
    {
        return $this->vatNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setVatNumber(?string $vatNumber): void
    {
        $this->vatNumber = VatNumberUtil::clean($vatNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function hasVatNumber(): bool
    {
        return !empty($this->getVatNumber());
    }
}
