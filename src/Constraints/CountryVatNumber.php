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

    /** @var string */
    public $message = 'prometee_sylius_vies_client.country_vat_number.invalid';

    /** @var string */
    public $vatNumberPath = 'vatNumber';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
