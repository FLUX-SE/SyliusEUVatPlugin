<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusVIESClientPlugin\Application\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareTrait;
use Sylius\Component\Core\Model\Address as BaseAddress;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_address")
 */
class Address extends BaseAddress implements AddressInterface
{
    use VATNumberAwareTrait;
}