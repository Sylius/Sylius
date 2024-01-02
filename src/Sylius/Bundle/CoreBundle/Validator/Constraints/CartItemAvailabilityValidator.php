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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class CartItemAvailabilityValidator extends ConstraintValidator
{
    public function __construct(private AvailabilityCheckerInterface $availabilityChecker)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        /** @var AddToCartCommandInterface $value */
        Assert::isInstanceOf($value, AddToCartCommandInterface::class);

        /** @var CartItemAvailability $constraint */
        Assert::isInstanceOf($constraint, CartItemAvailability::class);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $value->getCartItem();

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $cartItem->getVariant(),
            $cartItem->getQuantity() + $this->getExistingCartItemQuantityFromCart($value->getCart(), $cartItem),
        );

        if (!$isStockSufficient) {
            $this->context->addViolation(
                $constraint->message,
                ['%itemName%' => $cartItem->getVariant()->getInventoryName()],
            );
        }
    }

    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem): int
    {
        foreach ($cart->getItems() as $existingCartItem) {
            if ($existingCartItem->equals($cartItem)) {
                return $existingCartItem->getQuantity();
            }
        }

        return 0;
    }
}
