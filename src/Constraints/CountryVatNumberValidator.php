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
                ->addViolation();
        }
    }

    /**
     * @param VATNumberAwareInterface $value
     *
     * @return string|null
     */
    private function extractCountryCode(VATNumberAwareInterface $value): ?string
    {
        if (null === $value->getVatNumber()) {
            return null;
        }

        if ('' === $value->getVatNumber()) {
            return null;
        }

        $vatNumberArr = VatNumberUtil::split($value->getVatNumber());
        if (null === $vatNumberArr) {
            return null;
        }

        return $vatNumberArr[0];
    }
}
