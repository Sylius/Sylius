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
final class ProductAvailableInChannelValidator extends ConstraintValidator
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

        /** @var ProductAvailableInChannel $constraint */
        Assert::isInstanceOf($constraint, ProductAvailableInChannel::class);

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValue($value->getOrderTokenValue());

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $value->productVariantCode]);

        Assert::notNull($cartChannel = $cart->getChannel());

        /** @var ProductInterface $product */
        $product = $productVariant->getProduct();

        if (!$product->hasChannel($cartChannel)) {
            $this->context->addViolation(
                $constraint->message,
                ['%productName%' => $product->getName()]
            );
        }
    }
}
