<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Constraints;

use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CountryVatNumberValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CountryVatNumber) {
            throw new UnexpectedTypeException($constraint, CountryVatNumber::class);
        }

        if (!$value instanceof VATNumberAwareInterface) {
            throw new UnexpectedTypeException($value, VATNumberAwareInterface::class);
        }

        if (null === $value->getVatNumber() || '' === $value->getVatNumber()) {
            return;
        }

        $vatNumberArr = VatNumberUtil::split($value->getVatNumber());

        if (!$value instanceof AddressInterface) {
            throw new UnexpectedTypeException($value, VATNumberAwareInterface::class);
        }

        if ($vatNumberArr === null || $vatNumberArr[0] !== $value->getCountryCode()) {
            $this->context->buildViolation($constraint->message)
                ->setCode($constraint::CORRESPONDENCE_ERROR)
                ->atPath($constraint->vatNumberPath)
                ->addViolation();
        }
    }
}
