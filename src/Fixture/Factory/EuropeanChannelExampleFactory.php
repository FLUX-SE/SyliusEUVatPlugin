<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Fixture\Factory;

use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EuropeanChannelExampleFactory extends AbstractExampleFactory implements ExampleFactoryInterface
{
    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var RepositoryInterface */
    private $countryRepository;

    /** @var RepositoryInterface */
    private $zoneRepository;

    /** @var OptionsResolver */
    private $optionsResolver;

    public function __construct(
        ChannelRepositoryInterface $channelRepository,
        RepositoryInterface $countryRepository,
        RepositoryInterface $zoneRepository
    ) {
        $this->channelRepository = $channelRepository;
        $this->countryRepository = $countryRepository;
        $this->zoneRepository = $zoneRepository;

        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $options = []): EuropeanChannelAwareInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var EuropeanChannelAwareInterface|null $channel */
        $channel = $options['channel'];

        if ($channel === null) {
            throw new ChannelNotFoundException(
                sprintf(
                    'Channel "%s" has not been found, please create it before adding this fixture !',
                    $options['channel']
                )
            );
        }

        $channel->setBaseCountry($options['base_country']);
        $channel->setEuropeanZone($options['european_zone']);

        return $channel;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'channel',
                'base_country',
                'european_zone',
            ])
            ->setAllowedTypes('channel', ['string', ChannelInterface::class])
            ->setNormalizer('channel', LazyOption::findOneBy($this->channelRepository, 'code'))
            ->setAllowedTypes('base_country', ['string', CountryInterface::class])
            ->setNormalizer('base_country', LazyOption::findOneBy($this->countryRepository, 'code'))
            ->setAllowedTypes('european_zone', ['string', ZoneInterface::class])
            ->setNormalizer('european_zone', LazyOption::findOneBy($this->zoneRepository, 'code'))
        ;
    }
}
