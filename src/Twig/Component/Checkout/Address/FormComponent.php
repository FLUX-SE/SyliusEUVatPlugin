<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Twig\Component\Checkout\Address;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\AddressBookComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent as BaseFormComponent;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

/**
 * @property array<string, array<string, mixed>> $formValues
 */
class FormComponent extends BaseFormComponent
{
    /**
     * This is needed since the original property is private and therefore not exposed to the template
     */
    #[ExposeInTemplate(name: 'form', getter: 'getFormView')]
    private ?FormView $formView = null;

    #[LiveListener(AddressBookComponent::SYLIUS_SHOP_ADDRESS_UPDATED)]
    public function addressFieldUpdated(#[LiveArg] mixed $addressId, #[LiveArg] string $field): void
    {
        $customer = $this->customerContext->getCustomer();
        if (!$customer instanceof CustomerInterface) {
            return;
        }

        if (!is_scalar($addressId)) {
            return;
        }

        $address = $customer->getAddresses()->filter(static function (AddressInterface $address) use ($addressId): bool {
            $id = $address->getId();

            return (is_scalar($id) || $id instanceof \Stringable) && (string) $id === (string) $addressId;
        })->first();

        if (!$address instanceof VATNumberAwareInterface) {
            return;
        }

        parent::addressFieldUpdated($addressId, $field);
        $this->formValues[$field]['vatNumber'] = $address->getVatNumber();
    }
}
