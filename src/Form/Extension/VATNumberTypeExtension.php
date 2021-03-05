<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class VATNumberTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('vatNumber', TextType::class, [
                'label' => 'flux_se.sylius_eu_vat.form.vat_number',
                'required' => false,
            ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [AddressType::class];
    }
}
