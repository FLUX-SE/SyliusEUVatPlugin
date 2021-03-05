<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

final class EuropeanChannelAwareContext implements Context
{
    /** @var ObjectManager */
    private $channelManager;

    public function __construct(ObjectManager $channelManager)
    {
        $this->channelManager = $channelManager;
    }

    /**
     * @Given /^(its) based in the ("[^"]+" country) and allow VAT numbers for the ("[^"]+" zone)$/
     * @Given /^(this channel) is based in the ("[^"]+" country) and allow VAT numbers for the ("[^"]+" zone)$/
     */
    public function itsBasedInAndAllowVATNumbersForTheZone(EuropeanChannelAwareInterface $channel, CountryInterface $baseCountry, ZoneInterface $europeanZone)
    {
        $channel->setBaseCountry($baseCountry);
        $channel->setEuropeanZone($europeanZone);
        $this->channelManager->flush();
    }
}
