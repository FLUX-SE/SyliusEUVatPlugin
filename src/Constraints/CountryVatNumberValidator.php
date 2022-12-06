<?php

declare(strict_types=1);

namespace FluxSE\SyliusEUVatPlugin\Constraints;

use FluxSE\SyliusEUVatPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Sylius\Component\Core\Model\AddressInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CountryVatNumberValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CountryVatNumber) {
            throw new UnexpectedTypeException($constraint, CountryVatNumber::class);
        }

        if (false === $value instanceof AddressInterface) {
            throw new UnexpectedTypeException($value, AddressInterface::class);
        }

        if (!$value instanceof VATNumberAwareInterface) {
            throw new UnexpectedTypeException($value, VATNumberAwareInterface::class);
        }

        $countryCode = $this->extractCountryCode($value);
        if (null === $countryCode) {
            return;
        }

        if ($countryCode !== $value->getCountryCode()) {
            $this->context->buildViolation($constraint->message)
                ->setCode($constraint::CORRESPONDENCE_ERROR)
                ->atPath($constraint->vatNumberPath)
                ->addViolation()
            ;
        }
    }

    private function extractCountryCode(VATNumberAwareInterface $value): ?string
    {
        $vatNumber = $value->getVatNumber();
        if (null === $vatNumber) {
            return null;
        }

        if ('' === $vatNumber) {
            return null;
        }

        $vatNumberArr = VatNumberUtil::split($vatNumber);
        if (null === $vatNumberArr) {
            return null;
        }

        // Greece is the only case where the ISO code is not the same as the prefix
        if ('EL' === $vatNumberArr[0]) {
            return 'GR';
        }

        // Northern Ireland part of United Kingdom
        if ('XI' === $vatNumberArr[0]) {
            return 'GB';
        }

        return $vatNumberArr[0];
    }
}
