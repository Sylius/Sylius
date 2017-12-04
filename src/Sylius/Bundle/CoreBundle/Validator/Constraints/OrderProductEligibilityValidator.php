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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class OrderProductEligibilityValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function validate($order, Constraint $constraint): void
    {
        Assert::isInstanceOf($order, OrderInterface::class);

        /** @var OrderItemInterface[] $orderItems */
        $orderItems = $order->getItems();

        foreach ($orderItems as $orderItem) {
            if (!$orderItem->getProduct()->isEnabled()) {
                $this->context->addViolation(
                    $constraint->message,
                    ['%productName%' => $orderItem->getProduct()->getName()]
                );
            }
        }
    }
}
