<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Twig\Component\Checkout\Address;

use FluxSE\SyliusEUVatPlugin\Twig\Component\Checkout\Address\FormComponent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent as BaseFormComponent;
use Sylius\Component\Core\Model\Address as BaseAddress;
use Sylius\Component\Core\Model\Customer;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\AddressRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Tests\FluxSE\SyliusEUVatPlugin\App\Entity\Addressing\Address;

final class FormComponentTest extends TestCase
{
    private AddressRepositoryInterface&MockObject $addressRepository;

    private CustomerContextInterface&MockObject $customerContext;

    private FormComponent $component;

    protected function setUp(): void
    {
        /** @var OrderRepositoryInterface<OrderInterface>&MockObject $orderRepository */
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        /** @var UserRepositoryInterface<ShopUserInterface>&MockObject $shopUserRepository */
        $shopUserRepository = $this->createMock(UserRepositoryInterface::class);

        $this->addressRepository = $this->createMock(AddressRepositoryInterface::class);
        $this->customerContext = $this->createMock(CustomerContextInterface::class);
        $this->component = new FormComponent(
            $orderRepository,
            $this->createMock(FormFactoryInterface::class),
            Order::class,
            FormTypeInterface::class,
            $this->customerContext,
            $shopUserRepository,
            $this->addressRepository,
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
            ->expects(self::atLeastOnce())
            ->method('getCustomer')
            ->willReturn($customer)
        ;
        $this->addressRepository
            ->method('findOneByCustomer')
            ->with('42', $customer)
            ->willReturn($address)
        ;
        $this->addressRepository
            ->method('find')
            ->willReturn($address)
        ;

        $this->component->addressFieldUpdated($addressId, $field);

        self::assertSame('Jane', $this->component->formValues[$field]['firstName']);
        self::assertArrayHasKey('vatNumber', $this->component->formValues[$field]);
        self::assertSame('FR12345678901', $this->component->formValues[$field]['vatNumber']);
    }

    /**
     * @return iterable<string, array{int|string, string}>
     */
    public static function selectedAddresses(): iterable
    {
        yield 'billing address with a string ID' => ['42', 'billingAddress'];
        yield 'shipping address with an integer ID' => [42, 'shippingAddress'];
    }

    public function testItHydratesTheSelectedCustomerAddressWithoutAnAddressRepository(): void
    {
        $repositoryParameter = (new \ReflectionMethod(BaseFormComponent::class, '__construct'))->getParameters()[6];
        if (!$repositoryParameter->allowsNull()) {
            self::markTestSkipped('Sylius versions before 2.2.10 require an address repository.');
        }

        /** @var OrderRepositoryInterface<OrderInterface>&MockObject $orderRepository */
        $orderRepository = $this->createMock(OrderRepositoryInterface::class);
        /** @var UserRepositoryInterface<ShopUserInterface>&MockObject $shopUserRepository */
        $shopUserRepository = $this->createMock(UserRepositoryInterface::class);
        // Reflection allows this test to remain compatible with older, non-nullable constructor signatures.
        $component = (new \ReflectionClass(FormComponent::class))->newInstanceArgs([
            $orderRepository,
            $this->createMock(FormFactoryInterface::class),
            Order::class,
            FormTypeInterface::class,
            $this->customerContext,
            $shopUserRepository,
            null,
        ]);
        $customer = new Customer();
        $address = $this->createPartialMock(Address::class, ['getId']);
        $address->method('getId')->willReturn(42);
        $address->setFirstName('Jane');
        $address->setVatNumber('FR12345678901');
        $customer->addAddress($address);
        $this->customerContext->method('getCustomer')->willReturn($customer);

        $component->addressFieldUpdated('42', 'billingAddress');

        self::assertSame('Jane', $component->formValues['billingAddress']['firstName']);
        self::assertSame('FR12345678901', $component->formValues['billingAddress']['vatNumber']);
    }

    /**
     * @dataProvider invalidAddressIds
     */
    public function testItIgnoresAnUnknownOrAnotherCustomersAddress(string $addressId): void
    {
        $customer = new Customer();
        $address = $this->createPartialMock(Address::class, ['getId']);
        $address->method('getId')->willReturn(42);
        $customer->addAddress($address);
        $otherCustomersAddress = $this->createPartialMock(Address::class, ['getId']);
        $otherCustomersAddress->method('getId')->willReturn(84);
        (new Customer())->addAddress($otherCustomersAddress);
        $initialFormValues = ['billingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;
        $this->addressRepository
            ->expects(self::never())
            ->method('findOneByCustomer')
        ;
        $this->addressRepository
            ->expects(self::never())
            ->method('find')
        ;

        $this->component->addressFieldUpdated($addressId, 'billingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidAddressIds(): iterable
    {
        yield 'unknown address' => ['404'];
        yield 'another customer address' => ['84'];
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
        $this->addressRepository
            ->expects(self::never())
            ->method('findOneByCustomer')
        ;
        $this->addressRepository
            ->expects(self::never())
            ->method('find')
        ;

        $this->component->addressFieldUpdated('42', 'shippingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }

    public function testItIgnoresAnAddressThatDoesNotSupportVatNumbers(): void
    {
        $customer = new Customer();
        $address = $this->createPartialMock(BaseAddress::class, ['getId']);
        $address->method('getId')->willReturn(42);
        $customer->addAddress($address);
        $initialFormValues = ['billingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;
        $this->addressRepository
            ->expects(self::never())
            ->method('findOneByCustomer')
        ;
        $this->addressRepository
            ->expects(self::never())
            ->method('find')
        ;

        $this->component->addressFieldUpdated('42', 'billingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }

    public function testItIgnoresANonScalarAddressId(): void
    {
        $customer = new Customer();
        $initialFormValues = ['billingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;
        $this->customerContext->expects(self::once())->method('getCustomer')->willReturn($customer);
        $this->addressRepository->expects(self::never())->method('findOneByCustomer');
        $this->addressRepository->expects(self::never())->method('find');

        $this->component->addressFieldUpdated(['42'], 'billingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }
}
