<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CountryVatNumber extends Constraint
{
    const CORRESPONDENCE_ERROR = '403';

    public $message = 'prometee_sylius_vies_client.country_vat_number.invalid';

    public $vatNumberPath = 'vatNumber';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
