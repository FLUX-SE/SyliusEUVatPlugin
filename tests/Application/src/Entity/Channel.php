<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusVIESClientPlugin\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareTrait;
use Sylius\Component\Core\Model\Channel as BaseChannel;

/**
 * @ORM\Entity()
 * @ORM\Table(name="sylius_channel")
 */
class Channel extends BaseChannel implements ChannelInterface
{
    use EuropeanChannelAwareTrait;
}
