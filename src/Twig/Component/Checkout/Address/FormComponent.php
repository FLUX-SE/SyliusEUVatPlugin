<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Twig\Component\Checkout\Address;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\AddressBookComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\Checkout\Address\FormComponent as BaseFormComponent;
use Symfony\Component\Form\FormView;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

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
        parent::addressFieldUpdated($addressId, $field);

        /** @var VATNumberAwareInterface $address */
        $address = $this->addressRepository->find($addressId);
        $this->formValues[$field]['vatNumber'] = $address->getVatNumber();
    }
}
