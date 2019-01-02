<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Address as BaseAddress;

/**
 * @ORM\MappedSuperclass()
 * @ORM\Table(name="sylius_address")
 */
class Address extends BaseAddress implements AddressInterface
{
    use VATNumberAwareTrait;
}
