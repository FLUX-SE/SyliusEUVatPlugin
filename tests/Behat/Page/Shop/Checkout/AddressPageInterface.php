<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface as BaseAddressPageInterface;

interface AddressPageInterface extends BaseAddressPageInterface
{
    public function specifyShippingVatNumber(string $vatNumber): void;

    public function specifyBillingVatNumber(string $vatNumber): void;

    public function hasShippingVatNumberInput(): bool;

    public function hasBillingVatNumberInput(): bool;
}
