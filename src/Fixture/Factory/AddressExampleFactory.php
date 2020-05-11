<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Fixture\Factory;

use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Helper\ViesHelperInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AddressExampleFactory as BaseAddressExampleFactory;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressExampleFactory extends BaseAddressExampleFactory
{
    /** @var ViesHelperInterface */
    private $viesHelper;

    public function __construct(
        FactoryInterface $addressFactory,
        RepositoryInterface $countryRepository,
        RepositoryInterface $customerRepository,
        ViesHelperInterface $viesHelper
    ) {
        parent::__construct($addressFactory, $countryRepository, $customerRepository);

        $this->viesHelper = $viesHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('vat_number', null)
            ->setAllowedValues('vat_number', function ($value): bool {
                if ($value === null) {
                    return true;
                }

                return $this->viesHelper->isValid($value) > ViesHelperInterface::CHECK_STATUS_INVALID_WEBSERVICE;
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): AddressInterface
    {
        $address = parent::create($options);

        if ($address instanceof VATNumberAwareInterface) {
            $address->setVatNumber($options['vat_number']);
        }

        return $address;
    }
}
