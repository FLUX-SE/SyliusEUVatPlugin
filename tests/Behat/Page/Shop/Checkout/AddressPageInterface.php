<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\AddressPageInterface as BaseAddressPageInterface;

interface AddressPageInterface extends BaseAddressPageInterface
{
    /**
     * @param string $vatNumber
     */
    public function specifyShippingVatNumber($vatNumber);

    /**
     * @param string $vatNumber
     */
    public function specifyBillingVatNumber($vatNumber);
}
