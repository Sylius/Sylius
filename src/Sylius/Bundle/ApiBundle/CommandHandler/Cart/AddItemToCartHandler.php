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
use Sylius\Bundle\ApiBundle\Context\UserContextInterface;
use Sylius\Bundle\ApiBundle\Exception\ProductVariantUnprocessableException;
use Sylius\Bundle\ApiBundle\Exception\UnprocessableCartException;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddItemToCartHandler
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
        private ?UserContextInterface $userContext = null,
    ) {
    }

    public function __invoke(AddItemToCart $addItemToCart): OrderInterface
    {
        /** @var ProductVariantInterface|null $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' => $addItemToCart->productVariantCode]);

        if ($productVariant === null) {
            throw new ProductVariantUnprocessableException();
        }

        /** @var OrderInterface|null $cart */
        $cart = $this->orderRepository->findCartByTokenValue($addItemToCart->orderTokenValue);

        if ($cart === null) {
            throw new UnprocessableCartException();
        }

        $this->assertCartAccessible($cart);

        /** @var OrderItemInterface $cartItem */
        $cartItem = $this->cartItemFactory->createNew();
        $cartItem->setVariant($productVariant);

        $this->orderItemQuantityModifier->modify($cartItem, $addItemToCart->quantity);
        $this->orderModifier->addToOrder($cart, $cartItem);

        return $cart;
    }

    private function assertCartAccessible(OrderInterface $cart): void
    {
        if (null === $this->userContext) {
            return;
        }

        $currentUser = $this->userContext->getUser();

        if (null === $currentUser) {
            return;
        }

        if ($cart->isCreatedByGuest()) {
            return;
        }

        $cartCustomer = $cart->getCustomer();

        if (null === $cartCustomer || null === $cartCustomer->getUser()) {
            return;
        }

        if (
            $currentUser instanceof ShopUserInterface
            && $currentUser->getCustomer()?->getId() === $cartCustomer->getId()
        ) {
            return;
        }

        throw new NotFoundHttpException('Cart not found.');
    }
}
