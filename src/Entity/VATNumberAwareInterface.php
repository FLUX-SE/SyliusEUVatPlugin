<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

use Prometee\VIESClientBundle\Constraints\VatNumber;

interface VATNumberAwareInterface
{
    /**
     * @VatNumber(groups={"Default", "sylius"})
     *
     * @return string|null
     */
    public function getVatNumber(): ?string;

    /**
     * @param string|null $vatNumber
     */
    public function setVatNumber(?string $vatNumber): void;

    /**
     * @return bool
     */
    public function hasVatNumber(): bool;
}
