<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusVIESClientPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;

final class EuropeanChannelAwareContext implements Context
{
    /** @var ObjectManager */
    private $channelManager;

    /**
     * @param ObjectManager $channelManager
     */
    public function __construct(ObjectManager $channelManager)
    {
        $this->channelManager = $channelManager;
    }

    /**
     * @Given /^(its) based in the ("[^"]+" country) and allow VAT numbers for the ("[^"]+" zone)$/
     * @Given /^(this channel) is based in the ("[^"]+" country) and allow VAT numbers for the ("[^"]+" zone)$/
     *
     * @param EuropeanChannelAwareInterface $channel
     * @param CountryInterface $baseCountry
     * @param ZoneInterface $europeanZone
     */
    public function itsBasedInAndAllowVATNumbersForTheZone(EuropeanChannelAwareInterface $channel, CountryInterface $baseCountry, ZoneInterface $europeanZone)
    {
        $channel->setBaseCountry($baseCountry);
        $channel->setEuropeanZone($europeanZone);
        $this->channelManager->flush();
    }
}