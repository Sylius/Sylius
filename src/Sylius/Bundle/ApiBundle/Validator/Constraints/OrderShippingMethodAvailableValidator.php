<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderShippingMethodAvailableValidator extends ConstraintValidator
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var OrderShippingMethodAvailable $constraint */
        Assert::isInstanceOf($constraint, OrderShippingMethodAvailable::class);
        /** @var OrderTokenValueAwareInterface $value */
        Assert::isInstanceOf($value, OrderTokenValueAwareInterface::class);

        $order = $this->orderRepository->findOneBy(['tokenValue' => $value->getOrderTokenValue()]);

        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        foreach ($order->getShipments() as $shipment) {
            /** @var ShippingMethodInterface $shippingMethod */
            $shippingMethod = $shipment->getMethod();

            if ($shippingMethod->isEnabled() && $shippingMethod->getChannels()->contains($order->getChannel())) {
                continue;
            }

            $this->context->addViolation(
                $constraint->message,
                ['%shippingMethodName%' => $shippingMethod->getName()],
            );
        }
    }
}
