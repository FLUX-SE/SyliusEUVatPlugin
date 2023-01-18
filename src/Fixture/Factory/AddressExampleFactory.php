<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Fixture\Factory;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Helper\ViesHelperInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AddressExampleFactory as BaseAddressExampleFactory;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressExampleFactory extends BaseAddressExampleFactory
{
    /**
     * @param FactoryInterface<VATNumberAwareInterface> $addressFactory
     * @param RepositoryInterface<CountryInterface> $countryRepository
     * @param RepositoryInterface<CustomerInterface> $customerRepository
     */
    public function __construct(
        FactoryInterface $addressFactory,
        RepositoryInterface $countryRepository,
        RepositoryInterface $customerRepository,
        private ViesHelperInterface $viesHelper,
    ) {
        parent::__construct($addressFactory, $countryRepository, $customerRepository);
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('vat_number', null)
            ->setAllowedValues('vat_number', function (?string $value): bool {
                if (null === $value) {
                    return true;
                }

                return $this->viesHelper->isValid($value) > ViesHelperInterface::CHECK_STATUS_INVALID_WEBSERVICE;
            })
        ;
    }

    public function create(array $options = []): AddressInterface
    {
        $address = parent::create($options);

        $vatNumber = null;
        if (isset($options['vat_number'])) {
            $vatNumber = (string) $options['vat_number'];
        }

        if ($address instanceof VATNumberAwareInterface) {
            $address->setVatNumber($vatNumber);
        }

        return $address;
    }
}
