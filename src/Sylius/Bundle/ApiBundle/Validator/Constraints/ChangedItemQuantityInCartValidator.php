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

use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Exception\OrderItemNotFoundException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Repository\OrderItemRepositoryInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

final class ChangedItemQuantityInCartValidator extends ConstraintValidator
{
    /**
     * @param OrderItemRepositoryInterface<OrderItemInterface> $orderItemRepository
     */
    public function __construct(
        private OrderItemRepositoryInterface $orderItemRepository,
        private OrderRepositoryInterface $orderRepository,
        private AvailabilityCheckerInterface $availabilityChecker,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, ChangeItemQuantityInCart::class);

        /** @var ChangedItemQuantityInCart $constraint */
        Assert::isInstanceOf($constraint, ChangedItemQuantityInCart::class);

        /** @var OrderItemInterface|null $orderItem */
        $orderItem = $this->orderItemRepository->findOneByIdAndCartTokenValue(
            $value->orderItemId,
            $value->orderTokenValue,
        );

        if ($orderItem === null) {
            throw new OrderItemNotFoundException();
        }

        $productVariant = $orderItem->getVariant();

        if ($productVariant === null) {
            $this->context->addViolation(
                $constraint->productVariantNotLongerAvailable,
                ['%productVariantName%' => $orderItem->getVariantName()],
            );

            return;
        }

        $productVariantCode = $productVariant->getCode();

        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();
        if (!$product->isEnabled()) {
            $this->context->addViolation(
                $constraint->productNotExistMessage,
                ['%productName%' => $product->getName()],
            );

            return;
        }

        if (!$productVariant->isEnabled()) {
            $this->context->addViolation(
                $constraint->productVariantNotLongerAvailable,
                ['%productVariantName%' => $orderItem->getVariantName()],
            );

            return;
        }

        if (!$this->availabilityChecker->isStockSufficient($productVariant, $value->quantity)) {
            $this->context->addViolation(
                $constraint->productVariantNotSufficient,
                ['%productVariantCode%' => $productVariantCode],
            );

            return;
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($value->getOrderTokenValue());
        Assert::notNull($cart);
        $channel = $cart->getChannel();
        Assert::notNull($channel);

        if (!$product->hasChannel($channel)) {
            $this->context->addViolation(
                $constraint->productNotExistMessage,
                ['%productName%' => $product->getName()],
            );
        }
    }
}
