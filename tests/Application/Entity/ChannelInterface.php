<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusVIESClientPlugin\Application\Entity;

use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface as BaseChannelInterface;

interface ChannelInterface extends BaseChannelInterface, EuropeanChannelAwareInterface
{

}