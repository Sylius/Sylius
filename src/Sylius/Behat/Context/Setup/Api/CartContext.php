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

namespace Sylius\Behat\Context\Setup\Api;

use Behat\Behat\Context\Context;
use Sylius\Behat\Client\ApiClientInterface;
use Sylius\Behat\Client\Request;
use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

final class CartContext implements Context
{
    /** @var ApiClientInterface */
    private $cartsClient;

    /** @var ResponseCheckerInterface */
    private $responseChecker;

    /** @var SharedStorageInterface */
    private $sharedStorage;

    /** @var ProductVariantResolverInterface */
    private $productVariantResolver;

    public function __construct(
        ApiClientInterface $cartsClient,
        ResponseCheckerInterface $responseChecker,
        SharedStorageInterface $sharedStorage,
        ProductVariantResolverInterface $productVariantResolver
    ) {
        $this->cartsClient = $cartsClient;
        $this->responseChecker = $responseChecker;
        $this->sharedStorage = $sharedStorage;
        $this->productVariantResolver = $productVariantResolver;
    }

    /**
     * @Given /^I (?:have|had) (product "[^"]+") in the (cart)$/
     * @Given /^I (?:have|had) (product "[^"]+") added to the (cart)$/
     * @Given /^I added (product "[^"]+") to the (cart)$/
     * @Given /^the (?:customer|visitor) has (product "[^"]+") in the (cart)$/
     * @When /^the (?:customer|visitor) try to add (product "[^"]+") in the customer (cart)$/
     */
    public function iAddedProductToTheCart(ProductInterface $product, ?string $tokenValue): void
    {
        $tokenValue = $this->pickupCart($tokenValue);

        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productCode' => $product->getCode(),
            'productVariantCode' => $this->productVariantResolver->getVariant($product)->getCode(),
            'quantity' => 1,
        ]);

        $this->cartsClient->executeCustomRequest($request);
    }

    /**
     * @Given /^I have (product "[^"]+") with (product option "[^"]+") ([^"]+) in the (cart)$/
     */
    public function iAddThisProductWithToTheCart(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue,
        ?string $tokenValue
    ): void {
        $tokenValue = $this->pickupCart($tokenValue);

        $request = Request::customItemAction('shop', 'orders', $tokenValue, HttpRequest::METHOD_PATCH, 'items');

        $request->updateContent([
            'productCode' => $product->getCode(),
            'productVariantCode' => $this
                ->getProductVariantWithProductOptionAndProductOptionValue(
                    $product,
                    $productOption,
                    $productOptionValue
                )
                ->getCode(),
            'quantity' => 1,
        ]);

        $this->cartsClient->executeCustomRequest($request);

    }

    private function pickupCart(?string $tokenValue): string
    {
        if ($tokenValue === null) {
            $this->cartsClient->buildCreateRequest();
            $tokenValue = $this->responseChecker->getValue($this->cartsClient->create(), 'tokenValue');

            $this->sharedStorage->set('cart_token', $tokenValue);

            return $tokenValue;
        }
        return $tokenValue;
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
