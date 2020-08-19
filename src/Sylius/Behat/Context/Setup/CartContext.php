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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class CartContext implements Context
{
    /** @var MessageBusInterface */
    private $commandBus;

    /** @var ProductVariantResolverInterface */
    private $productVariantResolver;

    public function __construct(MessageBusInterface $commandBus, ProductVariantResolverInterface $productVariantResolver)
    {
        $this->commandBus = $commandBus;
        $this->productVariantResolver = $productVariantResolver;
    }

    /**
     * @Given /^I added (product "[^"]+") to the (cart)$/
     */
    public function iAddedProductToTheCart(ProductInterface $product, string $tokenValue): void
    {
        $this->commandBus->dispatch(AddItemToCart::createFromData(
            $tokenValue,
            $product->getCode(),
            $this->productVariantResolver->getVariant($product)->getCode(),
            1
        ));
    }

    /**
     * @Given /^I have (product "[^"]+") with (product option "[^"]+") ([^"]+) in the (cart)$/
     */
    public function iAddThisProductWithToTheCart(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue,
        string $tokenValue
    ): void {
        $this->commandBus->dispatch(AddItemToCart::createFromData(
            $tokenValue,
            $product->getCode(),
            $this
                ->getProductVariantWithProductOptionAndProductOptionValue(
                    $product,
                    $productOption,
                    $productOptionValue
                )
                ->getCode(),
            1
        ));
    }

    private function getProductVariantWithProductOptionAndProductOptionValue(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue
    ): ?ProductVariantInterface {
        foreach ($product->getVariants() as $productVariant) {
            /** @var ProductOptionValueInterface $variantProductOptionValue */
            foreach ($productVariant->getOptionValues() as $variantProductOptionValue) {
                if (
                    $variantProductOptionValue->getValue() === $productOptionValue &&
                    $variantProductOptionValue->getOption() === $productOption
                ) {
                    return $productVariant;
                }
            }
        }

        return null;
    }
}
