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

namespace Sylius\Bundle\ShopBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class EnabledOnlyProductsInCartSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CartContextInterface $cartContext,
        private readonly OrderModifierInterface $orderModifier,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->isCartSummaryRoute($request)) {
            return;
        }

        $cart = $this->getCurrentCart();
        if (null === $cart) {
            return;
        }

        $removedCount = $this->removeDisabledProducts($cart);
        if ($removedCount > 0) {
            $this->addWarningFlash($request, 'sylius.cart.product.not_available');
        }
    }

    private function isCartSummaryRoute(Request $request): bool
    {
        return $request->attributes->get('_route') === 'sylius_shop_cart_summary';
    }

    private function getCurrentCart(): ?OrderInterface
    {
        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();

            return $cart;
        } catch (\Throwable) {
            return null;
        }
    }

    private function removeDisabledProducts(OrderInterface $cart): int
    {
        $removed = 0;
        $channel = $cart->getChannel();

        $itemsToRemove = [];
        /** @var OrderItemInterface $item */
        foreach ($cart->getItems() as $item) {
            $variant = $item->getVariant();
            if ($variant === null) {
                continue;
            }
            /** @var ProductInterface $product */
            $product = $variant->getProduct();
            if ($product !== null && method_exists($product, 'isEnabled') && (!$product->isEnabled() || !$product->getChannels()->contains($channel))) {
                $itemsToRemove[] = $item;
            }
        }

        foreach ($itemsToRemove as $item) {
            $this->orderModifier->removeFromOrder($cart, $item);
            ++$removed;
        }
        $this->entityManager->flush();

        return $removed;
    }

    private function addWarningFlash(Request $request, string $message): void
    {
        $flashBag = FlashBagProvider::getFlashBag($request->getSession());
        $flashBag->add('warning', $message);
    }
}
