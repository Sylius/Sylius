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

namespace Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Sylius\Behat\Context\Setup\Checkout\AddressContext;
use Sylius\Behat\Context\Setup\Checkout\PaymentContext;
use Sylius\Behat\Context\Setup\Checkout\ShippingContext;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Bundle\ApiBundle\Command\Cart\ChangeItemQuantityInCart;
use Sylius\Bundle\ApiBundle\Command\Cart\PickupCart;
use Sylius\Bundle\ApiBundle\Command\Cart\RemoveItemFromCart;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final readonly class CartContext implements Context
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private MessageBusInterface $commandBus,
        private ProductVariantResolverInterface $productVariantResolver,
        private RandomnessGeneratorInterface $generator,
        private SharedStorageInterface $sharedStorage,
        private AddressContext $addressContext,
        private ShippingContext $shippingContext,
        private PaymentContext $paymentContext,
        private string $guestCartTokenFilePath,
    ) {
    }

    /**
     * @Given the customer has created empty cart
     */
    public function theCustomerHasTheCart(): void
    {
        $this->pickupCart();
    }

    /**
     * @Given /^I have(?:| added) (\d+) (product(?:|s) "[^"]+") (?:to|in) the (cart)$/
     */
    #[Given('/^I added (\d+) (products "[^"]+") to the (cart)$/')]
    #[Given('/^I added (\d+) of (them) to (?:the|my) (cart)$/')]
    public function iAddedGivenQuantityOfProductsToTheCart(int $quantity, ProductInterface $product, ?string $tokenValue): void
    {
        $this->addProductToCart($product, $tokenValue, $quantity);
    }

    #[Given('I proceeded through the checkout process')]
    public function iProceededThroughTheCheckoutProcess(): void
    {
        $this->addressContext->addressCart();
        $this->shippingContext->chooseShippingMethod();
        $this->paymentContext->choosePaymentMethod();
    }

    /**
     * @Given /^I added (products "([^"]+)" and "([^"]+)") to the (cart)$/
     * @Given /^I added (products "([^"]+)", "([^"]+)" and "([^"]+)") to the (cart)$/
     *
     * @param ProductInterface[] $products
     */
    public function iAddedProductsAndToTheCart(array $products, ?string $tokenValue): void
    {
        foreach ($products as $product) {
            $this->addProductToCart($product, $tokenValue);
        }
    }

    /**
     * @Given /^I have (product "[^"]+") in the (cart)$/
     * @Given /^I have (product "[^"]+") added to the (cart)$/
     * @Given /^the (?:customer|visitor) has (product "[^"]+") in the (cart)$/
     * @When /^the (?:customer|visitor) try to add (product "[^"]+") in the customer (cart)$/
     */
    #[Given('/^I added (product "[^"]+") to the (cart)$/')]
    #[Given('/^I added (this product) to the (cart)$/')]
    #[Given('/^I added (this product) to the (cart) again$/')]
    #[Given('/^the visitor added (product "[^"]+") to the (cart)$/')]
    #[Given('/^the customer added (product "[^"]+") to the (cart)$/')]
    public function iAddedProductToTheCart(ProductInterface $product, ?string $tokenValue): void
    {
        $this->addProductToCart($product, $tokenValue);
    }

    #[Given('/^I changed (product "[^"]+") quantity to (\d+) in my (cart)$/')]
    #[Given('/^the visitor changed (product "[^"]+") quantity to (\d+) in their (cart)$/')]
    #[Given('/^the visitor changed (this product) quantity to (\d+) in their (cart)$/')]
    public function iChangedProductQuantityInTheCart(ProductInterface $product, int $quantity, ?string $tokenValue): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->sharedStorage->get('order');
        $orderItemId = $cart->getItems()->filter(
            static fn (OrderItemInterface $orderItem): bool => $orderItem->getVariant()->getProduct() === $product,
        )->first()->getId();

        $this->commandBus->dispatch(new ChangeItemQuantityInCart(
            orderTokenValue: $tokenValue,
            orderItemId: $orderItemId,
            quantity: $quantity,
        ));
    }

    /**
     * @Given /^I have ("[^"]+" variant of product "[^"]+") in the (cart)$/
     * @Given /^I have ("[^"]+" variant of this product) in the (cart)$/
     */
    #[Given('/^I added ("[^"]+" variant of product "[^"]+") to the (cart)$/')]
    public function iHaveVariantOfProductInTheCart(ProductVariantInterface $productVariant, ?string $tokenValue): void
    {
        if ($tokenValue === null || !$this->doesCartWithTokenExist($tokenValue)) {
            $tokenValue = $this->pickupCart();
        }

        $this->commandBus->dispatch(new AddItemToCart(
            orderTokenValue: $tokenValue,
            productVariantCode: $productVariant->getCode(),
            quantity: 1,
        ));

        $this->sharedStorage->set('product', $productVariant->getProduct());
        $this->sharedStorage->set('variant', $productVariant);
    }

    #[Given('/^I added (product "[^"]+") with (product option "[^"]+") ([^"]+) to the (cart)$/')]
    public function iAddedProductWithOptionToTheCart(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue,
        ?string $tokenValue,
    ): void {
        if ($tokenValue === null) {
            $tokenValue = $this->pickupCart($tokenValue);
        }

        $this->commandBus->dispatch(new AddItemToCart(
            orderTokenValue: $tokenValue,
            productVariantCode: $this
                ->getProductVariantWithProductOptionAndProductOptionValue(
                    $product,
                    $productOption,
                    $productOptionValue,
                )
                ->getCode(),
            quantity: 1,
        ));
    }

    #[Given('/^I removed (product "[^"]+") from the (cart)$/')]
    public function iRemoveProductFromTheCart(ProductInterface $product, string $tokenValue): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->sharedStorage->get('order');
        $itemId = $cart->getItems()->filter(
            static fn (OrderItemInterface $orderItem): bool => $orderItem->getVariant()->getProduct() === $product,
        )->first()->getId();

        $this->commandBus->dispatch(new RemoveItemFromCart(
            orderTokenValue: $tokenValue,
            itemId: $itemId,
        ));
    }

    #[Given('/^I removed ("[^"]+" variant) from the (cart)$/')]
    public function iRemoveVariantFromTheCart(ProductVariantInterface $variant, string $tokenValue): void
    {
        /** @var OrderInterface $cart */
        $cart = $this->sharedStorage->get('order');
        $itemId = $cart->getItems()->filter(
            static fn (OrderItemInterface $orderItem): bool => $orderItem->getVariant() === $variant,
        )->first()->getId();

        $this->commandBus->dispatch(new RemoveItemFromCart(
            orderTokenValue: $tokenValue,
            itemId: $itemId,
        ));
    }

    /**
     * @Given /^this (cart) has promotion applied with coupon "([^"]+)"$/
     */
    public function thisCartHasCouponAppliedWithCode(?string $tokenValue, string $couponCode): void
    {
        if ($tokenValue === null) {
            $tokenValue = $this->pickupCart();
        }

        $updateCart = new UpdateCart(
            orderTokenValue: $tokenValue,
            couponCode: $couponCode,
        );

        $this->commandBus->dispatch($updateCart);
    }

    private function pickupCart(?string $tokenValue = 'cart'): string
    {
        $tokenValue = $tokenValue ?? $this->generator->generateUriSafeString(10);

        /** @var ChannelInterface $channel */
        $channel = $this->sharedStorage->get('channel');
        $channelCode = $channel->getCode();

        if ($this->sharedStorage->has('token') && $this->sharedStorage->has('user')) {
            $user = $this->sharedStorage->get('user');

            if ($user instanceof ShopUserInterface) {
                /** @var CustomerInterface $customer */
                $email = $user->getCustomer()->getEmail();
            }

            $this->sharedStorage->set('created_as_guest', false);
        } else {
            file_put_contents($this->guestCartTokenFilePath, $tokenValue);

            $this->sharedStorage->set('created_as_guest', true);
        }

        $pickupCart = new PickupCart(
            channelCode: $channelCode,
            localeCode: $channel->getDefaultLocale()->getCode(),
            email: $email ?? null,
            tokenValue: $tokenValue,
        );

        $message = $this->commandBus->dispatch($pickupCart);

        $this->sharedStorage->set('cart_token', $tokenValue);
        $this->sharedStorage->set(
            'order',
            $message->last(HandledStamp::class)->getResult(),
        );

        return $tokenValue;
    }

    private function getProductVariantWithProductOptionAndProductOptionValue(
        ProductInterface $product,
        ProductOptionInterface $productOption,
        string $productOptionValue,
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

    private function addProductToCart(ProductInterface $product, ?string $tokenValue, int $quantity = 1): void
    {
        if ($tokenValue === null || !$this->doesCartWithTokenExist($tokenValue)) {
            $tokenValue = $this->pickupCart($tokenValue);
        }

        $this->commandBus->dispatch(new AddItemToCart(
            orderTokenValue: $tokenValue,
            productVariantCode: $this->productVariantResolver->getVariant($product)->getCode(),
            quantity: $quantity,
        ));

        $this->sharedStorage->set('product', $product);
    }

    private function doesCartWithTokenExist(string $tokenValue): bool
    {
        return $this->orderRepository->findCartByTokenValue($tokenValue) !== null;
    }
}
