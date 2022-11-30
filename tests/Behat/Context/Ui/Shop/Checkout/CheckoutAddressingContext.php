<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Context\Ui\Shop\Checkout;

use Behat\Behat\Context\Context;
use FriendsOfBehat\PageObjectExtension\Page\UnexpectedPageException;
use InvalidArgumentException;
use Sylius\Behat\Service\SharedStorageInterface;
use Tests\FluxSE\SyliusEUVatPlugin\App\Entity\AddressInterface;
use Tests\FluxSE\SyliusEUVatPlugin\Behat\Page\Shop\Checkout\AddressPageInterface;
use Webmozart\Assert\Assert;

final class CheckoutAddressingContext implements Context
{
    /** @var AddressPageInterface */
    private $addressPage;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    public function __construct(
        AddressPageInterface $addressPage,
        SharedStorageInterface $sharedStorage
    ) {
        $this->addressPage = $addressPage;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given /^I specify the shipping vat number as "([^"]+)"$/
     */
    public function iSpecifyTheShippingVatNumberAs($vatNumber)
    {
        $this->addressPage->specifyShippingVatNumber($vatNumber);
    }

    /**
     * @Given /^I specify the billing vat number as "([^"]+)"$/
     */
    public function iSpecifyTheBillingVatNumberAs($vatNumber)
    {
        $this->addressPage->specifyBillingVatNumber($vatNumber);
    }

    /**
     * @Then /^I should be notified that the vat number in (shipping|billing) details is not corresponding with the selected country$/
     */
    public function iShouldBeNotifiedThatTheInShippingDetailsIsNotCorrespondingWithTheSelectedCountry($type)
    {
        $this->assertElementValidationMessage($type, 'vat number', 'This VAT number does not correspond to the selected country');
    }

    /**
     * @Then /^I should be notified that the vat number in (shipping|billing) details is not valid$/
     */
    public function iShouldBeNotifiedThatTheInShippingDetailsIsNotValid($type)
    {
        $this->assertElementValidationMessage($type, 'vat number', 'Invalid VAT Number');
    }

    /**
     * @param string $type
     * @param string $element
     * @param string $expectedMessage
     *
     * @throws InvalidArgumentException
     */
    private function assertElementValidationMessage($type, $element, $expectedMessage)
    {
        $element = sprintf('%s_%s', $type, str_replace(' ', '_', $element));
        Assert::true($this->addressPage->checkValidationMessageFor($element, $expectedMessage));
    }

    /**
     * @When /^I specified the shipping (address as "[^"]+", "[^"]+", "[^"]+", "[^"]+" for "[^"]+") with VAT number "([^"]+)"$/
     *
     * @throws UnexpectedPageException
     */
    public function iSpecifiedTheShippingAddressWithVATNumber(AddressInterface $address, string $vatNumber)
    {
        $address->setVatNumber($vatNumber);

        $this->addressPage->open();

        $key = sprintf(
            'billing_address_%s_%s',
            strtolower((string) $address->getFirstName()),
            strtolower((string) $address->getLastName())
        );
        $this->sharedStorage->set($key, $address);

        $this->addressPage->specifyBillingAddress($address);
        $this->iSpecifyTheBillingVatNumberAs($vatNumber);

        $key = sprintf(
            'shipping_address_%s_%s',
            strtolower((string) $address->getFirstName()),
            strtolower((string) $address->getLastName())
        );
        $this->sharedStorage->set($key, $address);

        $this->addressPage->nextStep();
    }
}
