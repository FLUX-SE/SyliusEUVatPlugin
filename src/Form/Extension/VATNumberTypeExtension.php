<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Form\Extension;

use FluxSE\SyliusEUVatPlugin\Entity\EuropeanChannelAwareInterface;
use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use FluxSE\SyliusEUVatPlugin\Matcher\CountryCodeZoneMatcherInterface;
use Sylius\Bundle\AddressingBundle\Form\Type\AddressType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfonycasts\DynamicForms\DependentField;
use Symfonycasts\DynamicForms\DynamicFormBuilder;

class VATNumberTypeExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly CountryCodeZoneMatcherInterface $countryCodeZoneMatcher,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder = new DynamicFormBuilder($builder);

        $builder
            ->addDependent('vatNumber', 'countryCode', function (
                DependentField $field,
                ?string $countryCode = null,
            ) use ($options) {
                if (null === $countryCode) {
                    return;
                }

                /** @var EuropeanChannelAwareInterface|null $channel */
                $channel = $options['channel'] ?? null;
                if (false === $this->isPartOfEUOrNoChannelAvailable($channel, $countryCode)) {
                    return;
                }

                $field->add(TextType::class, [
                    'label' => 'flux_se.sylius_eu_vat.form.vat_number',
                    'required' => false,
                ]);
            })
        ;

        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $formEvent) {
                /** @var VATNumberAwareInterface $data */
                $data = $formEvent->getData();
                $form = $formEvent->getForm();

                if ($form->has('vatNumber')) {
                    return;
                }

                $data->setVatNumber(null);
            },
        );
    }

    public static function getExtendedTypes(): iterable
    {
        return [AddressType::class];
    }

    private function isPartOfEUOrNoChannelAvailable(?EuropeanChannelAwareInterface $channel, string $countryCode): bool
    {
        if (null === $channel) {
            return true;
        }

        $zone = $channel->getEuropeanZone();
        if (null === $zone) {
            return false;
        }

        if (false === $this->countryCodeZoneMatcher->countryCodeBelongsToZone($countryCode, $zone)) {
            return false;
        }

        return true;
    }
}
