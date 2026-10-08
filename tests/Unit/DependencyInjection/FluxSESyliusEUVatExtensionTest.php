<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\DependencyInjection;

use FluxSE\SyliusEUVatPlugin\DependencyInjection\FluxSESyliusEUVatExtension;
use FluxSE\SyliusEUVatPlugin\Modifier\VATNumberAddressFormValuesModifier;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\SyliusShopBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class FluxSESyliusEUVatExtensionTest extends TestCase
{
    public function testItRegistersTheVatModifierWithoutOverridingTheCheckoutComponent(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', ['SyliusShopBundle' => SyliusShopBundle::class]);

        (new FluxSESyliusEUVatExtension())->load([], $container);

        $serviceId = 'flux_se.sylius_eu_vat.shop.modifier.vat_number_address_form_values';
        self::assertSame(VATNumberAddressFormValuesModifier::class, $container->getDefinition($serviceId)->getClass());
        self::assertArrayHasKey($serviceId, $container->findTaggedServiceIds('sylius_shop.modifier.address_form_values'));
        self::assertFalse($container->hasDefinition('flux_se.sylius_eu_vat.shop.twig.component.checkout.address.form'));
        self::assertFalse($container->hasDefinition('sylius_shop.twig.component.checkout.address.form'));
    }

    public function testItDoesNotRegisterTheVatModifierWithoutTheShopBundle(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles', []);

        (new FluxSESyliusEUVatExtension())->load([], $container);

        self::assertFalse($container->hasDefinition('flux_se.sylius_eu_vat.shop.modifier.vat_number_address_form_values'));
        self::assertSame([], $container->findTaggedServiceIds('sylius_shop.modifier.address_form_values'));
    }
}
