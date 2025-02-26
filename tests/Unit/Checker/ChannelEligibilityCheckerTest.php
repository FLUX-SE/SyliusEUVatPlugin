<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Checker;

use FluxSE\SyliusEUVatPlugin\Checker\ChannelEligibilityChecker;
use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Component\Addressing\Factory\ZoneFactoryInterface;
use Sylius\Component\Addressing\Matcher\ZoneMatcherInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Factory\AddressFactoryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChannelEligibilityCheckerTest extends WebTestCase
{
    /** @var ZoneRepositoryInterface<ZoneInterface>|MockObject */
    private ZoneRepositoryInterface|MockObject $zoneRepositoryMock;

    private ZoneMatcherInterface $zoneMatcher;

    /** @var ZoneFactoryInterface<ZoneInterface> */
    private ZoneFactoryInterface $zoneFactory;

    /** @var FactoryInterface<ZoneMemberInterface> */
    private FactoryInterface $zoneMemberFactory;

    /** @var AddressFactoryInterface<AddressInterface&VATNumberAwareInterface> */
    private AddressFactoryInterface $addressFactory;

    /** @var ChannelFactoryInterface<ChannelInterface&EuropeanChannelAwareInterface> */
    private ChannelFactoryInterface $channelFactory;

    protected function setUp(): void
    {
        $this->zoneRepositoryMock = $this->createMock(ZoneRepositoryInterface::class);
        static::getContainer()->set('sylius.repository.zone', $this->zoneRepositoryMock);
        $this->zoneMatcher = static::getContainer()->get('sylius.matcher.zone');
        $this->zoneFactory = static::getContainer()->get('sylius.factory.zone');
        $this->zoneMemberFactory = static::getContainer()->get('sylius.factory.zone_member');
        $this->addressFactory = static::getContainer()->get('sylius.factory.address');
        $this->channelFactory = static::getContainer()->get('sylius.factory.channel');
    }

    /**
     * @dataProvider getTestsConfig
     *
     * @param string[] $availableCountries
     */
    public function testCheck(
        array $availableCountries,
        string $addressCountryCode,
        bool $expectedResult,
    ): void {
        $eligibilityChecker = new ChannelEligibilityChecker($this->zoneMatcher);

        /** @var ZoneInterface $euZone */
        $euZone = $this->zoneFactory->createNew();
        $euZone->setType(ZoneInterface::TYPE_COUNTRY);
        foreach ($availableCountries as $countryCode) {
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($countryCode);
            $euZone->addMember($zoneMember);
        }

        $this->zoneRepositoryMock
            ->expects(self::once())
            ->method('findByAddress')
            ->willReturnCallback(function () use ($euZone, $expectedResult) {
                if ($expectedResult) {
                    return [$euZone];
                }

                return [];
            })
        ;

        $this->zoneRepositoryMock
            ->expects(self::once())
            ->method('findByMembers')
            ->willReturnCallback(function () {
                return [];
            })
        ;

        /** @var AddressInterface&VATNumberAwareInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($addressCountryCode);

        /** @var ChannelInterface&EuropeanChannelAwareInterface $channel */
        $channel = $this->channelFactory->createNew();
        $channel->setEuropeanZone($euZone);

        $result = $eligibilityChecker->check($address, $channel);

        self::assertEquals($expectedResult, $result);
    }

    /**
     * @return iterable<array{string[], string, bool}>
     */
    public static function getTestsConfig(): iterable
    {
        yield [
            [
                'FR',
                'GB',
            ],
            'FR',
            true,
        ];
        yield [
            [
                'FR',
                'GB',
            ],
            'GB',
            true,
        ];
        yield [
            [
                'FR',
                'GB',
            ],
            'XX',
            false,
        ];
        yield [
            [
                'FR',
                'ES',
            ],
            'GB',
            false,
        ];
    }
}
