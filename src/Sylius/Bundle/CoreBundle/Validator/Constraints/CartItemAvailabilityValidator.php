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

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class CartItemAvailabilityValidator extends ConstraintValidator
{
    /**
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * @param AvailabilityCheckerInterface $availabilityChecker
     */
    public function __construct(AvailabilityCheckerInterface $availabilityChecker)
    {
        $this->availabilityChecker = $availabilityChecker;
    }

    /**
     * @param AddToCartCommandInterface $addCartItemCommand
     *
     * {@inheritdoc}
     */
    public function validate($addCartItemCommand, Constraint $constraint)
    {
        Assert::isInstanceOf($addCartItemCommand, AddToCartCommandInterface::class);
        Assert::isInstanceOf($constraint, CartItemAvailability::class);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $addCartItemCommand->getCartItem();

        $isStockSufficient = $this->availabilityChecker->isStockSufficient(
            $cartItem->getVariant(),
            $cartItem->getQuantity() + $this->getExistingCartItemQuantityFromCart($addCartItemCommand->getCart(), $cartItem)
        );

        if (!$isStockSufficient) {
            $this->context->addViolation(
                $constraint->message,
                ['%itemName%' => $cartItem->getVariant()->getInventoryName()]
            );
        }
    }

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $cartItem
     *
     * @return int
     */
    private function getExistingCartItemQuantityFromCart(OrderInterface $cart, OrderItemInterface $cartItem)
    {
        foreach ($cart->getItems() as $existingCartItem) {
            if ($existingCartItem->equals($cartItem)) {
                return $existingCartItem->getQuantity();
            }
        }

        return 0;
    }
}
