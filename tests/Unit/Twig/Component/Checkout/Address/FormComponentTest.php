<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Twig\Component\Checkout\Address;

use FluxSE\SyliusEUVatPlugin\Modifier\VATNumberAddressFormValuesModifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Modifier\DefaultAddressFormValuesModifier;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent;
use Sylius\Component\Core\Model\Address as BaseAddress;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tests\FluxSE\SyliusEUVatPlugin\App\Entity\Addressing\Address;

final class FormComponentTest extends TestCase
{
    private CustomerContextInterface&MockObject $customerContext;

    private FormComponent $component;

    protected function setUp(): void
    {
        /** @var OrderRepositoryInterface<OrderInterface>&MockObject $orderRepository */
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        /** @var UserRepositoryInterface<ShopUserInterface>&MockObject $shopUserRepository */
        $shopUserRepository = $this->createMock(UserRepositoryInterface::class);

        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->component = new FormComponent(
            $orderRepository,
            $this->createMock(FormFactoryInterface::class),
            Order::class,
            FormTypeInterface::class,
            $this->customerContext,
            $shopUserRepository,
            null,
            [new DefaultAddressFormValuesModifier(), new VATNumberAddressFormValuesModifier()],
        );
    }

    /**
     * @dataProvider selectedAddresses
     */
    public function testItHydratesTheSelectedCustomerAddressIncludingItsVatNumber(int|string $addressId, string $field): void
    {
        $customer = new Customer();
        $address = $this->createPartialMock(Address::class, ['getId']);
        $address->method('getId')->willReturn(42);
        $customer->addAddress($address);
        $address->setFirstName('Jane');
        $address->setLastName('Doe');
        $address->setPhoneNumber('+33102030405');
        $address->setCompany('Acme');
        $address->setCountryCode('FR');
        $address->setProvinceName('Île-de-France');
        $address->setStreet('1 Main Street');
        $address->setCity('Paris');
        $address->setPostcode('75001');
        $address->setVatNumber('FR12345678901');

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;

        $this->component->addressFieldUpdated($addressId, $field);

        $addressValues = $this->component->formValues[$field];
        self::assertIsArray($addressValues);
        self::assertSame('Jane', $addressValues['firstName']);
        self::assertSame('FR', $addressValues['countryCode']);
        self::assertSame('Île-de-France', $addressValues['provinceName']);
        self::assertSame('FR12345678901', $addressValues['vatNumber']);
    }

    /**
     * @return iterable<string, array{int|string, string}>
     */
    public static function selectedAddresses(): iterable
    {
        yield 'billing address with a string ID' => ['42', 'billingAddress'];
        yield 'shipping address with an integer ID' => [42, 'shippingAddress'];
    }

    /**
     * @dataProvider selectedAddresses
     */
    public function testItClearsThePreviousVatNumberWhenSelectingAnAddressWithoutOne(int|string $addressId, string $field): void
    {
        $customer = new Customer();
        $address = $this->createPartialMock(Address::class, ['getId']);
        $address->method('getId')->willReturn(42);
        $customer->addAddress($address);
        $this->customerContext->method('getCustomer')->willReturn($customer);
        $this->component->formValues = [$field => ['vatNumber' => 'FR12345678901']];

        $this->component->addressFieldUpdated($addressId, $field);

        $addressValues = $this->component->formValues[$field];
        self::assertIsArray($addressValues);
        self::assertArrayHasKey('vatNumber', $addressValues);
        self::assertNull($addressValues['vatNumber']);
    }

    /**
     * @dataProvider invalidAddressIds
     */
    public function testItIgnoresAnUnavailableAddress(mixed $addressId): void
    {
        $customer = new Customer();
        $address = $this->createPartialMock(Address::class, ['getId']);
        $address->method('getId')->willReturn(84);
        $address->setVatNumber('FR12345678901');
        (new Customer())->addAddress($address);
        $initialFormValues = ['billingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;

        $this->component->addressFieldUpdated($addressId, 'billingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }

    /**
     * @return iterable<string, array{mixed}>
     */
    public static function invalidAddressIds(): iterable
    {
        yield 'unknown address' => ['404'];
        yield 'another customer address' => ['84'];
        yield 'non-scalar address ID' => [[]];
        yield 'null address ID' => [null];
    }

    public function testItIgnoresAddressSelectionWithoutAnAuthenticatedCustomer(): void
    {
        $initialFormValues = ['shippingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn(null)
        ;

        $this->component->addressFieldUpdated('42', 'shippingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }

    public function testItPreservesStandardAddressHydrationWhenVatNumbersAreNotSupported(): void
    {
        $customer = new Customer();
        $address = $this->createPartialMock(BaseAddress::class, ['getId']);
        $address->method('getId')->willReturn(42);
        $address->setFirstName('Jane');
        $customer->addAddress($address);

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;

        $this->component->addressFieldUpdated('42', 'billingAddress');

        $addressValues = $this->component->formValues['billingAddress'];
        self::assertIsArray($addressValues);
        self::assertSame('Jane', $addressValues['firstName']);
        self::assertArrayNotHasKey('vatNumber', $addressValues);
    }
}
