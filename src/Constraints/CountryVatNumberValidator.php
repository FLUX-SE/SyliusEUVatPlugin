<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Constraints;

use Prometee\VIESClient\Helper\ViesHelperInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class VatNumberValidator extends ConstraintValidator
{

    /** @var ViesHelperInterface */
    protected $helper;

    /**
     * @param ViesHelperInterface $helper
     */
    public function __construct(ViesHelperInterface $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @return ViesHelperInterface
     */
    public function getHelper(): ViesHelperInterface
    {
        return $this->helper;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof VatNumber) {
            throw new UnexpectedTypeException($constraint, VatNumber::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $status = $this->helper->isValid($value);
        switch ($status) {
            case ViesHelperInterface::CHECK_STATUS_INVALID_WEBSERVICE:
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode($constraint::WRONG_NUMBER_ERROR)
                    ->addViolation();
                break;
            case ViesHelperInterface::CHECK_STATUS_INVALID:
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ value }}', $this->formatValue($value))
                    ->setCode($constraint::WRONG_FORMAT_ERROR)
                    ->addViolation();
                break;
        }
    }

    protected function formatValue($value, $format = 0)
    {
        $value = VatNumberUtil::clean($value);
        return parent::formatValue($value, $format);
    }
}
