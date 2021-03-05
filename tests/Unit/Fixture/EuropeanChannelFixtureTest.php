<?php

declare(strict_types=1);

namespace Tests\Sylius\SyliusEUVatPlugin\Unit\Fixture;

use Doctrine\Persistence\ObjectManager;
use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use PHPUnit\Framework\TestCase;
use FluxSE\SyliusEUVatPlugin\Fixture\EuropeanChannelFixture;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;

final class EuropeanChannelFixtureTest extends TestCase
{
    use ConfigurationTestCaseTrait;

    /** @test */
    public function european_channel_are_optional(): void
    {
        $this->assertConfigurationIsValid([[]], 'custom');
    }

    /** @test */
    public function european_channel_can_be_generated_randomly(): void
    {
        $this->assertConfigurationIsValid([['random' => 4]], 'random');
        $this->assertPartialConfigurationIsInvalid([['random' => -1]], 'random');
    }

    /** @test */
    public function european_channel_channel_is_required(): void
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['base_country' => 'CUSTOM', 'european_zone' => 'CUSTOM']]]], 'custom.*.channel');
    }

    /** @test */
    public function european_channel_base_country_is_required(): void
    {
        $this->assertPartialConfigurationIsInvalid([['custom' => [['channel' => 'CUSTOM', 'european_zone' => 'CUSTOM']]]], 'custom.*.base_country');
    }

    /** @test */
    public function european_channel_european_zone_is_required(): void
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
