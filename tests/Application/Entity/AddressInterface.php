<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusVIESClientPlugin\Application\Entity;

use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Core\Model\AddressInterface as BaseAddressInterface;

interface AddressInterface extends BaseAddressInterface, VATNumberAwareInterface
{
}