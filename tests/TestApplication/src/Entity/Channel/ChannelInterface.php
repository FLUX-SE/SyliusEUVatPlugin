<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\App\Entity\Channel;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface, EuropeanChannelAwareInterface
{
}
