<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class VatNumber extends Constraint
{
    const IS_A_SUBSCRIPTION_ERROR = '403';

    protected static $errorNames = [
        self::IS_A_SUBSCRIPTION_ERROR => 'ALREADY_USED_LICENCE_CODE_ERROR',
    ];

    public $message = 'app.b2b.order_item.is_a_subscription';

    public $vatNumberPath = 'vat_number';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
