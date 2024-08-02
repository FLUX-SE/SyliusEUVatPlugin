<?php

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
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ChannelEligibilityCheckerTest extends WebTestCase
{
    private ZoneRepositoryInterface&MockObject $zoneRepositoryMock;
    private ZoneMatcherInterface $zoneMatcher;
    private ZoneFactoryInterface $zoneFactory;
    /** @var FactoryInterface<ZoneMemberInterface>  */
    private FactoryInterface $zoneMemberFactory;
    private AddressFactoryInterface $addressFactory;
    private ChannelFactoryInterface $channelFactory;

    protected function setUp(): void
    {
        $this->zoneRepositoryMock = $this->createMock(ZoneRepositoryInterface::class);
        static::getContainer()->set('sylius.repository.zone', $this->zoneRepositoryMock);
        $this->zoneMatcher = static::getContainer()->get('sylius.zone_matcher');
        $this->zoneFactory = static::getContainer()->get('sylius.factory.zone');
        $this->zoneMemberFactory = static::getContainer()->get('sylius.factory.zone_member');
        $this->addressFactory = static::getContainer()->get('sylius.factory.address');
        $this->channelFactory = static::getContainer()->get('sylius.factory.channel');
    }

    /**
     * @dataProvider getTestsConfig
     * @param string[] $availableCountries
     */
    public function testCheck(
        array $availableCountries,
        string $addressCountryCode,
        bool $expectedResult,
    ): void {
        $eligibilityChecker = new ChannelEligibilityChecker($this->zoneMatcher);

        $euZone = $this->zoneFactory->createNew();
        foreach ($availableCountries as $countryCode) {
            $zoneMember = $this->zoneMemberFactory->createNew();
            $zoneMember->setCode($countryCode);
            $euZone->addMember($zoneMember);
        }

        $this->zoneRepositoryMock
            ->expects($this->once())
            ->method('findByAddress')
            ->willReturnCallback(function (AddressInterface $address) use ($euZone) {
                $zones = [];
                foreach ($euZone->getMembers() as $zoneMember) {
                    if ($zoneMember->getCode() === $address->getCountryCode()) {
                        $zones[] = $euZone;
                    }
                }

                return $zones;
            })
        ;

        $this->zoneRepositoryMock
            ->expects($this->once())
            ->method('findByMembers')
            ->willReturn([])
        ;

        /** @var AddressInterface&VATNumberAwareInterface $address */
        $address = $this->addressFactory->createNew();
        $address->setCountryCode($addressCountryCode);
        /** @var ChannelInterface&EuropeanChannelAwareInterface $channel */
        $channel = $this->channelFactory->createNew();
        $channel->setEuropeanZone($euZone);

        $result = $eligibilityChecker->check($address, $channel);

        $this->assertEquals($expectedResult, $result);
    }

    public static function getTestsConfig(): iterable
    {
        yield [
            [
                'FR',
                'GB'
            ],
            'FR',
            true
        ];
        yield [
            [
                'FR',
                'GB'
            ],
            'GB',
            true
        ];
        yield [
            [
                'FR',
                'GB'
            ],
            'XX',
            false
        ];
        yield [
            [
                'FR',
                'ES'
            ],
            'GB',
            false
        ];
    }
}
