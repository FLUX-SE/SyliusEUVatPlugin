<?php

declare(strict_types=1);

namespace Prometee\SyliusVIESClientPlugin\Applicator;

use InvalidArgumentException;
use Prometee\SyliusVIESClientPlugin\Entity\EuropeanChannelAwareInterface;
use Prometee\SyliusVIESClientPlugin\Entity\VATNumberAwareInterface;
use Prometee\VIESClient\Helper\ViesHelperInterface;
use Prometee\VIESClient\Util\VatNumberUtil;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Webmozart\Assert\Assert;

class OrderEuropeanVATNumberApplicator implements OrderTaxesApplicatorInterface
{
    /** @var ViesHelperInterface */
    protected $viesHelper;

    /**
     * @param ViesHelperInterface $viesHelper
     */
    public function __construct(ViesHelperInterface $viesHelper)
    {
        $this->viesHelper = $viesHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        /** @var EuropeanChannelAwareInterface $channel */
        $channel = $order->getChannel();
        if (
            $channel !== null
            && $channel->getEuropeanZone() !== null
            && $channel->getBaseCountry() !== null
            && $order->getBillingAddress() !== null
        ) {
            // These weird assignments are required for PHPStan
            $billingCountryCode = $order->getBillingAddress()->getCountryCode();
            /** @var VATNumberAwareInterface $billingAddress */
            $billingAddress = $order->getBillingAddress();

            if ($this->isValidForZeroEuropeanVAT($billingAddress, $billingCountryCode, $zone, $channel)) {
                foreach ($order->getItems() as $item) {
                    $quantity = $item->getQuantity();
                    Assert::notSame($quantity, 0, 'Cannot apply tax to order item with 0 quantity.');

                    $item->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
                }
            }
        }
    }

    /**
     * @param VATNumberAwareInterface $billingAddress
     * @param string $billingCountryCode
     * @param ZoneInterface $zone
     * @param EuropeanChannelAwareInterface $channel
     * @return bool
     */
    public function isValidForZeroEuropeanVAT(
        VATNumberAwareInterface $billingAddress,
        string $billingCountryCode,
        ZoneInterface $zone,
        EuropeanChannelAwareInterface $channel
    ): bool
    {
        if ($billingAddress->hasVatNumber()) {
            $vatNumberArr = VatNumberUtil::split($billingAddress->getVatNumber());
            if (
                $vatNumberArr !== null
                && $zone === $channel->getEuropeanZone()
                && $channel->getBaseCountry() !== null
                && $channel->getBaseCountry()->getCode() !== $billingCountryCode
                && $billingCountryCode === $vatNumberArr[0]
            ) {
                return true;
            }
        }

        return false;
    }
}
