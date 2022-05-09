<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Core\Taxation\Applicator;

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Order\Factory\AdjustmentFactoryInterface;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Resolver\TaxRateResolverInterface;
use Webmozart\Assert\Assert;

class OrderShipmentTaxesApplicator implements OrderTaxesApplicatorInterface
{
    public function __construct(
        private CalculatorInterface $calculator,
        private AdjustmentFactoryInterface $adjustmentFactory,
        private TaxRateResolverInterface $taxRateResolver
    ) {
    }

    public function apply(OrderInterface $order, ZoneInterface $zone): void
    {
        if (0 === $order->getShippingTotal()) {
            return;
        }

        if (!$order->hasShipments()) {
            throw new \LogicException('Order should have at least one shipment.');
        }

        foreach ($order->getShipments() as $shipment) {
            $shippingMethod = $this->getShippingMethod($shipment);

            /** @var TaxRateInterface|null $taxRate */
            $taxRate = $this->taxRateResolver->resolve($shippingMethod, ['zone' => $zone]);
            if (null === $taxRate) {
                continue;
            }

            $taxAmount = $this->calculator->calculate($shipment->getAdjustmentsTotal(), $taxRate);
            if (0.00 === $taxAmount) {
                continue;
            }

            $this->addAdjustment($shipment, (int) $taxAmount, $taxRate, $shippingMethod);
        }
    }

    private function addAdjustment(
        ShipmentInterface $shipment,
        int $taxAmount,
        TaxRateInterface $taxRate,
        ShippingMethodInterface $shippingMethod
    ): void {
        $shipment->addAdjustment($this->adjustmentFactory->createWithData(
            AdjustmentInterface::TAX_ADJUSTMENT,
            $taxRate->getLabel(),
            $taxAmount,
            $taxRate->isIncludedInPrice(),
            [
                'shippingMethodCode' => $shippingMethod->getCode(),
                'shippingMethodName' => $shippingMethod->getName(),
                'taxRateCode' => $taxRate->getCode(),
                'taxRateName' => $taxRate->getName(),
                'taxRateAmount' => $taxRate->getAmount(),
            ]
        ));
    }

    /**
     * @throws \LogicException
     */
    private function getShippingMethod(ShipmentInterface $shipment): ShippingMethodInterface
    {
        $method = $shipment->getMethod();

        /** @var ShippingMethodInterface $method */
        Assert::isInstanceOf($method, ShippingMethodInterface::class);

        return $method;
    }
}
