<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Behat\Context\Api\Shop\Checkout;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Resources;
use Sylius\Behat\Context\Api\Shop\CheckoutContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class CheckoutAddressingContext implements Context
{
    /** @var array{
     *     shippingAddress?: array<string, string|null>,
     *     billingAddress?: array<string, string|null>,
     * }
     */
    private array $content = [];

    public function __construct(
        private readonly ApiClientInterface $client,
        private readonly SharedStorageInterface $sharedStorage,
        private readonly ResponseCheckerInterface $responseChecker,
        private readonly CheckoutContext $checkoutContext,
    ) {
    }

    /**
     * @When I proceed with :shippingMethod shipping method and :paymentMethod payment
     */
    public function iProceedOrderWithShippingMethodAndPayment(
        ShippingMethodInterface $shippingMethod,
        PaymentMethodInterface $paymentMethod,
    ): void{
        $this->checkoutContext->iProceedOrderWithShippingMethodAndPayment($shippingMethod, $paymentMethod);
    }

    /**
     * @Then I should be on the checkout summary step
     */
    public function iShouldBeOnTheCheckoutCompleteStep(): void
    {
        $this->checkoutContext->iShouldBeOnTheCheckoutCompleteStep();
    }

    /**
     * @Then /^my tax total should be ("[^"]+")$/
     */
    public function myTaxTotalShouldBe(int $taxTotal): void
    {
        $this->checkoutContext->myTaxTotalShouldBe($taxTotal);
    }

    /**
     * @Given I am at the checkout addressing step
     */
    public function iAmAtTheCheckoutAddressingStep(): void
    {
        $this->checkoutContext->iAmAtTheCheckoutAddressingStep();
    }

    /**
     * @When I complete the addressing step
     * @When I try to complete the addressing step
     */
    public function iCompleteTheAddressingStep(): void
    {
        $this->addressOrder($this->content);

        $this->content = [];
    }

    /** @param array<array-key, mixed> $content */
    private function addressOrder(array $content): void
    {
        $this->client
            ->buildUpdateRequest(Resources::ORDERS, $this->getCartTokenValue())
            ->setRequestData($content)
            ->update()
        ;
    }

    private function getCartTokenValue(): ?string
    {
        if ($this->sharedStorage->has('cart_token')) {
            return $this->sharedStorage->get('cart_token');
        }

        if ($this->sharedStorage->has('previous_cart_token')) {
            return $this->sharedStorage->get('previous_cart_token');
        }

        return null;
    }

    /**
     * @When /^I specify the(?:| required) shipping (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I specify the shipping (address for "([^"]+)" from "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function iSpecifyTheShippingAddressAs(AddressInterface $address): void
    {
        $this->fillAddress('shippingAddress', $address);
    }

    /**
     * @When /^I specify(?: the| different) billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^the (?:customer|visitor) specify the billing (address as "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)" for "([^"]+)")$/
     * @When /^I specify the billing (address for "([^"]+)" from "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)", "([^"]+)")$/
     */
    public function iSpecifyTheBillingAddressAs(AddressInterface $address): void
    {
        $this->fillAddress('billingAddress', $address);
    }

    private function fillAddress(string $addressType, AddressInterface $address): void
    {
        $this->content[$addressType]['city'] = $address->getCity() ?? '';
        $this->content[$addressType]['street'] = $address->getStreet() ?? '';
        $this->content[$addressType]['postcode'] = $address->getPostcode() ?? '';
        $this->content[$addressType]['countryCode'] = $address->getCountryCode() ?? '';
        $this->content[$addressType]['firstName'] = $address->getFirstName() ?? '';
        $this->content[$addressType]['lastName'] = $address->getLastName() ?? '';
        $this->content[$addressType]['provinceName'] = $address->getProvinceName();
    }

    /**
     * @When /^I specify the (shipping|billing) vat number as "([^"]+)"$/
     * @When /^I try to specify the (shipping|billing) vat number as "([^"]+)"$/
     */
    public function iSpecifyTheVatNumberAs(string $type, string $vatNumber): void
    {
        $this->content[$type.'Address']['vatNumber'] = $vatNumber;
    }

    /**
     * @Then /I should not be able to specify VAT number manually for (billing address|shipping address)$/
     */
    public function iShouldNotBeAbleToSpecifyVATNumberManuallyForAddress(string $addressType): void
    {
        if ($this->content !== []) {
            $this->iCompleteTheAddressingStep();
        }

        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'Invalid VAT Number',
            sprintf('%s.%s', StringInflector::nameToCamelCase($addressType), 'vatNumber'),
        ));
    }

    /**
     * @Then /^I should be notified that the VAT number in (shipping|billing) details is not corresponding with the selected country$/
     */
    public function iShouldBeNotifiedThatTheInShippingDetailsIsNotCorrespondingWithTheSelectedCountry(string $type): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'This VAT number does not correspond to the selected country',
        ));
    }

    /**
     * @Then /^I should be notified that the VAT number in (shipping|billing) details is not valid$/
     */
    public function iShouldBeNotifiedThatTheInShippingDetailsIsNotValid(string $type): void
    {
        Assert::true($this->isViolationWithMessageInResponse(
            $this->client->getLastResponse(),
            'Invalid VAT Number',
            sprintf('%sAddress.%s', $type, 'vatNumber'),
        ));
    }

    private function isViolationWithMessageInResponse(Response $response, string $message, ?string $property = null): bool
    {
        $violations = $this->responseChecker->getResponseContent($response)['violations'];
        foreach ($violations as $violation) {
            if ($violation['message'] === $message && $property === null) {
                return true;
            }

            if ($violation['message'] === $message && $property !== null && $violation['propertyPath'] === $property) {
                return true;
            }
        }

        return false;
    }
}
