<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

interface VATNumberAwareInterface
{
    /**
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
