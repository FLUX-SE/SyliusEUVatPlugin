<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\App\Entity\Channel;

use Doctrine\ORM\Mapping as ORM;
use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

#[ORM\Table(name: 'sylius_channel')]
#[ORM\Entity]
class Channel extends BaseChannel implements ChannelInterface
{
    use EuropeanChannelAwareTrait;
}
