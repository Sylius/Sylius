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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Cart;

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final readonly class AddItemToCartHandler implements MessageHandlerInterface
{
    /**
     * @param OrderRepositoryInterface<OrderInterface> $orderRepository
     * @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository
     */
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private OrderModifierInterface $orderModifier,
        private CartItemFactoryInterface $cartItemFactory,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
    ) {
    }

    public function __invoke(AddItemToCart $addItemToCart): OrderInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $addItemToCart->productVariantCode]);

        if ($productVariant === null) {
            throw new \InvalidArgumentException('Product variant with given code has not been found.');
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($addItemToCart->orderTokenValue);

        if ($cart === null) {
            throw new \InvalidArgumentException('Cart with given token has not been found.');
        }

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createNew();
        $cartItem->setVariant($productVariant);

        $this->orderItemQuantityModifier->modify($cartItem, $addItemToCart->quantity);
        $this->orderModifier->addToOrder($cart, $cartItem);

        return $cart;
    }
}
