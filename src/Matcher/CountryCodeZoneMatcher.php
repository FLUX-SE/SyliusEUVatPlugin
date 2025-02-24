<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Matcher;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Addressing\Model\ZoneMemberInterface;
use Sylius\Component\Addressing\Repository\ZoneRepositoryInterface;

final readonly class CountryCodeZoneMatcher implements CountryCodeZoneMatcherInterface
{
    /**
     * @param ZoneRepositoryInterface<ZoneInterface> $zoneRepository
     */
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {
    }

    public function countryCodeBelongsToZoneMember(string $countryCode, ZoneMemberInterface $member): bool
    {
        switch ($type = $member->getBelongsTo()?->getType()) {
            case ZoneInterface::TYPE_PROVINCE:
            case ZoneInterface::TYPE_COUNTRY:
                return $countryCode === $member->getCode();
            case ZoneInterface::TYPE_ZONE:
                $zone = $this->zoneRepository->findOneBy(['code' => $member->getCode()]);

                return null !== $zone && $this->countryCodeBelongsToZone($countryCode, $zone);
            default:
                throw new \InvalidArgumentException(sprintf('Unexpected zone type "%s".', $type));
        }
    }

    public function countryCodeBelongsToZone(string $countryCode, ZoneInterface $zone): bool
    {
        foreach ($zone->getMembers() as $member) {
            if ($this->countryCodeBelongsToZoneMember($countryCode, $member)) {
                return true;
            }
        }

        return false;
    }
}
