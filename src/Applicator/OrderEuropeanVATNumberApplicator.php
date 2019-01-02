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
        ) {
            /** @var VATNumberAwareInterface|AddressInterface $billingAddress */
            $billingAddress = $order->getBillingAddress();
            if ($billingAddress->hasVatNumber()) {
                $billingCountryCode = $billingAddress->getCountryCode();
                $vatNumberArr = VatNumberUtil::split($billingAddress->getVatNumber());
                if (
                    $vatNumberArr !== null
                    && $zone === $channel->getEuropeanZone()
                    && $channel->getBaseCountry()->getCode() !== $billingCountryCode
                    && $billingCountryCode === $vatNumberArr[0]
                ) {
                    foreach ($order->getItems() as $item) {
                        $quantity = $item->getQuantity();
                        Assert::notSame($quantity, 0, 'Cannot apply tax to order item with 0 quantity.');

                        $item->removeAdjustmentsRecursively(AdjustmentInterface::TAX_ADJUSTMENT);
                    }
                }
            }
        }
    }
}
