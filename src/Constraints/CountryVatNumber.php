<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Constraints;

use Symfony\Component\Validator\Constraint;

class CountryVatNumber extends Constraint
{
    /** @var string */
    public const CORRESPONDENCE_ERROR = '403';

    public string $message = 'flux_se.sylius_eu_vat.country_vat_number.invalid';

    public string $vatNumberPath = 'vatNumber';

    public function getTargets(): string|array
    {
        return self::CLASS_CONSTRAINT;
    }
}
