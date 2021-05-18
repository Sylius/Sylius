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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class AddingEligibleProductVariantToCartValidator extends ConstraintValidator
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var AvailabilityCheckerInterface  */
    private $availabilityChecker;

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository,
        AvailabilityCheckerInterface $availabilityChecker
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->orderRepository = $orderRepository;
        $this->availabilityChecker = $availabilityChecker;
    }

    public function validate($value, Constraint $constraint)
    {
        Assert::isInstanceOf($value, AddItemToCart::class);

        /** @var AddingEligibleProductVariantToCart $constraint */
        Assert::isInstanceOf($constraint, AddingEligibleProductVariantToCart::class);

        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' =>$value->productVariantCode]);

        if ($productVariant === null) {
            $this->context->addViolation(
                $constraint->productVariantNotExistMessage,
                ['%productVariantCode%' => $value->productVariantCode]
            );

            return;
        }

        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();
        if (!$product->isEnabled()) {
            $this->context->addViolation(
                $constraint->productNotExistMessage,
                ['%productName%' => $product->getName()]
            );

            return;
        }

        if (!$productVariant->isEnabled()) {
            $this->context->addViolation(
                $constraint->productVariantNotExistMessage,
                ['%productVariantCode%' => $productVariant->getCode()]
            );

            return;
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($value->getOrderTokenValue());
        Assert::notNull($cart);

        $units = $cart->getItemUnits();

        $sameOrderItemsCounter = 0;

        if ($productVariant->isTracked() && !$units->isEmpty()) {
            foreach ($units as $unit) {
                /** @var OrderItemInterface $orderItem */
                $orderItem = $unit->getOrderItem();

                /** @var ProductVariantInterface */
                $variant = $orderItem->getVariant();

                $code = $variant->getCode();

                if ($value->productVariantCode = $code) {
                    ++$sameOrderItemsCounter;
                }
            }
        }

        if (!$this->availabilityChecker->isStockSufficient($productVariant, $value->quantity + $sameOrderItemsCounter)) {
            $this->context->addViolation(
                $constraint->productVariantNotSufficient,
                ['%productVariantCode%' => $productVariant->getCode()]
            );

            return;
        }

        $channel = $cart->getChannel();
        Assert::notNull($channel);

        if (!$product->hasChannel($channel)) {
            $this->context->addViolation(
                $constraint->productNotExistMessage,
                ['%productName%' => $product->getName()]
            );
        }
    }
}
