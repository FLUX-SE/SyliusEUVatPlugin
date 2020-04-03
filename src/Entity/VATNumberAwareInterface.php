<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

interface VATNumberAwareInterface
{
    public function getVatNumber(): ?string;

    public function setVatNumber(?string $vatNumber): void;

    public function hasVatNumber(): bool;
}
