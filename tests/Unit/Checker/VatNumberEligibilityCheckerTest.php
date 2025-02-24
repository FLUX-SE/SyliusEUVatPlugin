<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Checker;

use FluxSE\SyliusEUVatPlugin\Checker\VatNumberEligibilityChecker;
use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class VatNumberEligibilityCheckerTest extends WebTestCase
{
    /** @var AddressFactoryInterface<AddressInterface&VATNumberAwareInterface> */
    private AddressFactoryInterface $addressFactory;

    /** @var ChannelFactoryInterface<ChannelInterface&EuropeanChannelAwareInterface> */
    private ChannelFactoryInterface $channelFactory;

    protected function setUp(): void
    {
        $this->addressFactory = static::getContainer()->get('sylius.factory.address');
        $this->channelFactory = static::getContainer()->get('sylius.factory.channel');
    }

    /**
     * @dataProvider getTestsConfig
     */
    public function testCheck(
        string $addressCountryCode,
        string $addressVatNumber,
        bool $expectedResult,
    ): void {
        $eligibilityChecker = new VatNumberEligibilityChecker();

        /** @var AddressInterface&VATNumberAwareInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($addressCountryCode);
        $address->setVatNumber($addressVatNumber);
        /** @var ChannelInterface&EuropeanChannelAwareInterface $channel */
        $channel = $this->channelFactory->createNew();

        $result = $eligibilityChecker->check($address, $channel);

        self::assertEquals($expectedResult, $result);
    }

    public static function getTestsConfig(): iterable
    {
        yield [
            'FR',
            'FR12345678987',
            true,
        ];
        yield [
            'GB',
            'XI12345678987',
            true,
        ];
        yield [
            'GB',
            'GB12345678987',
            false,
        ];
        yield [
            'GR',
            'EL12345678987',
            true,
        ];
        yield [
            'GR',
            'GR12345678987',
            false,
        ];
    }
}
