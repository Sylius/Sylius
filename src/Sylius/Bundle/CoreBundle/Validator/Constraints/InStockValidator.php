<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class InStockValidator extends ConstraintValidator
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @var CartContextInterface
     */
    private $cartContext;

    /**
     * @param AvailabilityCheckerInterface $availabilityChecker
     * @param CartContextInterface $cartContext
     */
    public function __construct(AvailabilityCheckerInterface $availabilityChecker, CartContextInterface $cartContext)
    {
        $this->availabilityChecker = $availabilityChecker;
        $this->cartContext = $cartContext;
    }

    /**
     * @param OrderItemInterface $orderItem
     *
     * {@inheritDoc}
     */
    public function validate($orderItem, Constraint $constraint)
    {
        Assert::isInstanceOf($orderItem, OrderItemInterface::class);
        Assert::isInstanceOf($constraint, InStock::class);

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $orderItem->getVariant(),
            $orderItem->getQuantity() + $this->getExistingOrderItemQuantity($orderItem)
        );

        if (!$isStockSufficient) {
            $this->context->addViolation(
                $constraint->message,
                ['%stockable%' => $orderItem->getVariant()->getInventoryName()]
            );
        }
    }

    /**
     * @param OrderItemInterface $existingOrderItem
     *
     * @return int
     */
    private function getExistingOrderItemQuantity(OrderItemInterface $existingOrderItem)
    {
        foreach ($this->cartContext->getCart()->getItems() as $orderItem) {
            if ($orderItem->equals($existingOrderItem)) {
                return $orderItem->getQuantity();
            }
        }

        return 0;
    }
}
