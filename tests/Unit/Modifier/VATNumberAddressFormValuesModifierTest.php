<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Modifier;

use FluxSE\SyliusEUVatPlugin\Modifier\VATNumberAddressFormValuesModifier;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\Address as BaseAddress;
use Tests\FluxSE\SyliusEUVatPlugin\App\Entity\Addressing\Address;

final class VATNumberAddressFormValuesModifierTest extends TestCase
{
    public function testItAddsTheVatNumberWithoutReplacingExistingAddressData(): void
    {
        $address = new Address();
        $address->setVatNumber('FR12345678901');
        $addressData = ['firstName' => 'Jane', 'countryCode' => 'FR', 'customField' => 'Preserved'];

        $modifiedData = (new VATNumberAddressFormValuesModifier())->modify($addressData, $address);

        self::assertSame($addressData + ['vatNumber' => 'FR12345678901'], $modifiedData);
    }

    public function testItClearsAnExistingVatNumberWhenTheAddressHasNone(): void
    {
        $modifiedData = (new VATNumberAddressFormValuesModifier())->modify(
            ['firstName' => 'Jane', 'vatNumber' => 'FR12345678901'],
            new Address(),
        );

        self::assertSame(['firstName' => 'Jane', 'vatNumber' => null], $modifiedData);
    }

    public function testItLeavesAddressDataUnchangedWhenVatNumbersAreNotSupported(): void
    {
        $addressData = ['firstName' => 'Jane', 'customField' => 'Preserved'];

        self::assertSame(
            $addressData,
            (new VATNumberAddressFormValuesModifier())->modify($addressData, new BaseAddress()),
        );
    }
}
