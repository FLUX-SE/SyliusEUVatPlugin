<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CountryVatNumber extends Constraint
{
    public const CORRESPONDENCE_ERROR = '403';

    /** @var string */
    public string $message = 'flux_se.sylius_eu_vat.country_vat_number.invalid';

    /** @var string[] */
    public $groups = [];

    /** @var string */
    public $vatNumberPath = 'vatNumber';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
