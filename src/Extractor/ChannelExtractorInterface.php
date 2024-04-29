<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Extractor;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;

interface ChannelExtractorInterface
{
    /**
     * @return (ChannelInterface&EuropeanChannelAwareInterface)|null
     */
    public function extract(OrderInterface $order): ?EuropeanChannelAwareInterface;
}
