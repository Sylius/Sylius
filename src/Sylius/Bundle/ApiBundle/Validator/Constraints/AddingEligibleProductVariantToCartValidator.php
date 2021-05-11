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
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
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

    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->productVariantRepository = $productVariantRepository;
        $this->orderRepository = $orderRepository;
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
