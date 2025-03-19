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

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class PlacedOrderCartItemsImmutableValidator extends ConstraintValidator
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @param AddItemToCart $value
     * @param PlacedOrderCartItemsImmutable $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::true(
            is_a($value, AddItemToCart::class) ||
            is_a($value, ChangeItemQuantityInCart::class) ||
            is_a($value, RemoveItemFromCart::class),
        );
        Assert::isInstanceOf($constraint, PlacedOrderCartItemsImmutable::class);
        Assert::string($value->orderTokenValue, 'Order token value has to be a string.');

        $order = $this->orderRepository->findOneWithCompletedCheckout($value->orderTokenValue);

        if ($order === null) {
            return;
        }

        if ($order->getState() === BaseOrderInterface::STATE_NEW) {
            $this->context->addViolation($constraint->message);
        }
    }
}
