<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('baseCountry', CountryChoiceType::class, [
                'label' => 'flux_se.sylius_eu_vat.form.base_country',
                'required' => false,
            ])
            ->add('europeanZone', ZoneChoiceType::class, [
                'label' => 'flux_se.sylius_eu_vat.form.european_zone',
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
