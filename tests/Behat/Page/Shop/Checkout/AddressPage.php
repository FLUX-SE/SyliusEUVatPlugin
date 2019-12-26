<?php

declare(strict_types=1);

namespace Tests\Prometee\SyliusVIESClientPlugin\Behat\Page\Shop\Checkout;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Shop\Checkout\AddressPage as BaseAddressPage;

class AddressPage extends BaseAddressPage implements AddressPageInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'billing_vat_number' => '#sylius_checkout_address_billingAddress_vatNumber',
            'shipping_vat_number' => '#sylius_checkout_address_shippingAddress_vatNumber',
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ElementNotFoundException
     */
    public function specifyShippingVatNumber($vatNumber)
    {
        $this->getElement('shipping_vat_number')->setValue($vatNumber);
    }

    /**
     * {@inheritdoc}
     *
     * @throws ElementNotFoundException
     */
    public function specifyBillingVatNumber($vatNumber)
    {
        $this->getElement('billing_vat_number')->setValue($vatNumber);
    }
}
