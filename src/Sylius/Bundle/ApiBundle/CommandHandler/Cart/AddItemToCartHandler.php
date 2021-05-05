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
use Webmozart\Assert\Assert;

/** @experimental */
final class AddItemToCartHandler implements MessageHandlerInterface
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    /** @var OrderModifierInterface */
    private $orderModifier;

    /** @var CartItemFactoryInterface */
    private $cartItemFactory;

    /** @var OrderItemQuantityModifierInterface */
    private $orderItemQuantityModifier;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ProductVariantRepositoryInterface $productVariantRepository,
        OrderModifierInterface $orderModifier,
        CartItemFactoryInterface $cartItemFactory,
        OrderItemQuantityModifierInterface $orderItemQuantityModifier
    ) {
        $this->orderRepository = $orderRepository;
        $this->productVariantRepository = $productVariantRepository;
        $this->orderModifier = $orderModifier;
        $this->cartItemFactory = $cartItemFactory;
        $this->orderItemQuantityModifier = $orderItemQuantityModifier;
    }

    public function __invoke(AddItemToCart $addItemToCart): OrderInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $addItemToCart->productVariantCode]);

        Assert::notNull($productVariant);

        /** @var OrderInterface $cart */
        $cart = $this->orderRepository->findCartByTokenValue($addItemToCart->orderTokenValue);

        Assert::notNull($cart);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createNew();
        $cartItem->setVariant($productVariant);

        $this->orderItemQuantityModifier->modify($cartItem, $addItemToCart->quantity);
        $this->orderModifier->addToOrder($cart, $cartItem);

        return $cart;
    }
}
