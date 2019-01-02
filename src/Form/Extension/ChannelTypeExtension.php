<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryChoiceType;
use Sylius\Bundle\AddressingBundle\Form\Type\ZoneChoiceType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ChannelTypeExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('baseCountry', CountryChoiceType::class, [
                'label' => 'prometee_sylius_vies_client.form.base_country',
                'required' => false,
            ])
            ->add('europeanZone', ZoneChoiceType::class, [
                'label' => 'prometee_sylius_vies_client.form.european_zone',
                'required' => false,
            ])
        ;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChannelType::class];
    }
}
