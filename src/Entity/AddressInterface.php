<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

use Sylius\Component\Core\Model\AddressInterface as BaseAddressInterface;

interface AddressInterface extends BaseAddressInterface, VATNumberAwareInterface
{
}
