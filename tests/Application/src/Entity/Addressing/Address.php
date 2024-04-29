<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\App\Entity\Addressing;

use Doctrine\ORM\Mapping as ORM;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareTrait;
use Sylius\Component\Core\Model\Address as BaseAddress;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_address")
 */
#[ORM\Table(name: 'sylius_address')]
#[ORM\Entity]
class Address extends BaseAddress implements AddressInterface
{
    use VATNumberAwareTrait;
}
