<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Page\Shop\Checkout;

use Sylius\Behat\Page\Shop\Checkout\AddressPage as BaseAddressPage;

class AddressPage extends BaseAddressPage implements AddressPageInterface
{
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_vat_number' => '#sylius_checkout_address_billingAddress_vatNumber',
            'shipping_vat_number' => '#sylius_checkout_address_shippingAddress_vatNumber',
        ]);
    }

    public function specifyShippingVatNumber(string $vatNumber): void
    {
        $this->getElement('shipping_vat_number')->setValue($vatNumber);
    }

    public function specifyBillingVatNumber(string $vatNumber): void
    {
        $this->getElement('billing_vat_number')->setValue($vatNumber);
    }
}
