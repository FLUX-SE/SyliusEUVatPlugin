<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Page\Shop\Checkout;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Shop\Checkout\AddressPage as BaseAddressPage;

class AddressPage extends BaseAddressPage implements AddressPageInterface
{
    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_vat_number' => '[data-test-billing-address] [data-test-vat-number]',
            'shipping_vat_number' => '[data-test-shipping-address] [data-test-vat-number]',
        ]);
    }

    public function specifyShippingVatNumber(string $vatNumber): void
    {
        $this->waitForElementUpdate('form');

        try {
            $this->getElement('shipping_vat_number')->setValue($vatNumber);
        } catch (ElementNotFoundException) {
            // Do nothing
        }
    }

    public function specifyBillingVatNumber(string $vatNumber): void
    {
        $this->waitForElementUpdate('form');

        try {
            $this->getElement('billing_vat_number')->setValue($vatNumber);
        } catch (ElementNotFoundException) {
            // Do nothing
        }
    }

    public function hasShippingVatNumberInput(): bool
    {
        $this->waitForElementUpdate('form');

        return $this->hasElement('shipping_vat_number');
    }

    public function hasBillingVatNumberInput(): bool
    {
        $this->waitForElementUpdate('form');

        return $this->hasElement('billing_vat_number');
    }
}
