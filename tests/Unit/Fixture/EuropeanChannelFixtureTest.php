<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use FluxSE\SyliusEUVatPlugin\Fixture\EuropeanChannelFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class EuropeanChannelFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    public function testEuropeanChannelAreOptional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    public function testEuropeanChannelCanBeGeneratedRandomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    public function testEuropeanChannelChannelIsRequired(): void
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['base_country' => 'CUSTOM', 'european_zone' => 'CUSTOM']]]], 'custom.*.channel');
    }

    public function testEuropeanChannelBaseCountryIsRequired(): void
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['channel' => 'CUSTOM', 'european_zone' => 'CUSTOM']]]], 'custom.*.base_country');
    }

    public function testEuropeanChannelEuropeanZoneIsRequired(): void
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['base_country' => 'CUSTOM', 'channel' => 'CUSTOM']]]], 'custom.*.european_zone');
    }

    protected function getConfiguration(): EuropeanChannelFixture
    {
        return new EuropeanChannelFixture(
            $this->getMockBuilder(ObjectManager::class)->getMock(),
            $this->getMockBuilder(ExampleFactoryInterface::class)->getMock()
        );
    }
}
