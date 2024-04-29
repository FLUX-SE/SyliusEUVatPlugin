<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Extractor;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ChannelExtractor implements ChannelExtractorInterface
{

    public function extract(OrderInterface $order): ?EuropeanChannelAwareInterface
    {
        $channel = $order->getChannel();
        if (false === $channel instanceof EuropeanChannelAwareInterface) {
            return null;
        }

        if (null === $channel->getBaseCountry()) {
            return null;
        }

        if (null === $channel->getEuropeanZone()) {
            return null;
        }

        return $channel;
    }
}
