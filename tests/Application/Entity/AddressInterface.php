<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Application\Entity;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\AddressInterface as BaseAddressInterface;

interface AddressInterface extends BaseAddressInterface, VATNumberAwareInterface
{
}