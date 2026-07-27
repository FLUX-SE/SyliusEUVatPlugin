<?php

declare(strict_types=1);

namespace Tests\FluxSE\SyliusEUVatPlugin\Unit\Twig\Component\Checkout\Address;

use FluxSE\SyliusEUVatPlugin\Twig\Component\Checkout\Address\FormComponent;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
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

    public function testItHydratesTheSelectedCustomerAddressIncludingItsVatNumber(): void
    {
        $customer = new Customer();
        $address = new Address();
        $address->setCustomer($customer);
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
            ->expects(self::atLeastOnce())
            ->method('findOneByCustomer')
            ->with('42', $customer)
            ->willReturn($address)
        ;
        $this->addressRepository
            ->method('find')
            ->willReturn($address)
        ;

        $this->component->addressFieldUpdated('42', 'billingAddress');

        self::assertSame([
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'phoneNumber' => '+33102030405',
            'company' => 'Acme',
            'countryCode' => 'FR',
            'provinceName' => 'Île-de-France',
            'street' => '1 Main Street',
            'city' => 'Paris',
            'postcode' => '75001',
            'vatNumber' => 'FR12345678901',
        ], $this->component->formValues['billingAddress']);
    }

    /**
     * @dataProvider invalidAddressIds
     */
    public function testItIgnoresAnUnknownOrAnotherCustomersAddress(string $addressId): void
    {
        $customer = new Customer();
        $initialFormValues = ['billingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;
        $this->addressRepository
            ->expects(self::once())
            ->method('findOneByCustomer')
            ->with($addressId, $customer)
            ->willReturn(null)
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
        $address = new \Sylius\Component\Core\Model\Address();
        $initialFormValues = ['billingAddress' => ['firstName' => 'Unchanged']];
        $this->component->formValues = $initialFormValues;

        $this->customerContext
            ->expects(self::once())
            ->method('getCustomer')
            ->willReturn($customer)
        ;
        $this->addressRepository
            ->expects(self::once())
            ->method('findOneByCustomer')
            ->with('42', $customer)
            ->willReturn($address)
        ;
        $this->addressRepository
            ->expects(self::never())
            ->method('find')
        ;

        $this->component->addressFieldUpdated('42', 'billingAddress');

        self::assertSame($initialFormValues, $this->component->formValues);
    }
}
