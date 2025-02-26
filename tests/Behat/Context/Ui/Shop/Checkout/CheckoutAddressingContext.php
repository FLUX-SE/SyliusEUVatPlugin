<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use Tests\FluxSE\SyliusEUVatPlugin\Behat\Page\Shop\Checkout\AddressPageInterface;
use Webmozart\Assert\Assert;

final class CheckoutAddressingContext implements Context
{
    public function __construct(
        private AddressPageInterface $addressPage,
    ) {
    }

    /**
     * @When /^I specify the shipping vat number as "([^"]+)"$/
     * @When /^I try to specify the shipping vat number as "([^"]+)"$/
     */
    public function iSpecifyTheShippingVatNumberAs(string $vatNumber): void
    {
        $this->addressPage->specifyShippingVatNumber($vatNumber);
    }

    /**
     * @When /^I specify the billing vat number as "([^"]+)"$/
     * @When /^I try to specify the billing vat number as "([^"]+)"$/
     */
    public function iSpecifyTheBillingVatNumberAs(string $vatNumber): void
    {
        $this->addressPage->specifyBillingVatNumber($vatNumber);
    }

    /**
     * @Then I should not be able to specify VAT number manually for shipping address
     */
    public function iShouldNotBeAbleToSpecifyVATNumberManuallyForShippingAddress(): void
    {
        Assert::false($this->addressPage->hasShippingVatNumberInput());
    }

    /**
     * @Then I should not be able to specify VAT number manually for billing address
     */
    public function iShouldNotBeAbleToSpecifyVATNumberManuallyForBillingAddress(): void
    {
        Assert::false($this->addressPage->hasBillingVatNumberInput());
    }

    /**
     * @Then /^I should be notified that the VAT number in (shipping|billing) details is not corresponding with the selected country$/
     */
    public function iShouldBeNotifiedThatTheInShippingDetailsIsNotCorrespondingWithTheSelectedCountry(string $type): void
    {
        $this->assertElementValidationMessage($type, 'vat number', 'This VAT number does not correspond to the selected country');
    }

    /**
     * @Then /^I should be notified that the VAT number in (shipping|billing) details is not valid$/
     */
    public function iShouldBeNotifiedThatTheInShippingDetailsIsNotValid(string $type): void
    {
        $this->assertElementValidationMessage($type, 'vat number', 'Invalid VAT Number');
    }

    private function assertElementValidationMessage(string $type, string $element, string $expectedMessage): void
    {
        $element = sprintf('%s_%s', $type, str_replace(' ', '_', $element));
        Assert::true($this->addressPage->checkValidationMessageFor($element, $expectedMessage));
    }
}
