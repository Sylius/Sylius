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

namespace Sylius\Bundle\CoreBundle\Collector;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * @internal
 */
final class CartCollector extends DataCollector
{
    public function __construct(private CartContextInterface $cartContext)
    {
        $this->data = [];
    }

    public function hasCart(): bool
    {
        return $this->data !== [];
    }

    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function getTotal(): ?int
    {
        return $this->data['total'] ?? null;
    }

    public function getSubtotal(): ?int
    {
        return $this->data['subtotal'] ?? null;
    }

    public function getCurrency(): ?string
    {
        return $this->data['currency'] ?? null;
    }

    public function getLocale(): ?string
    {
        return $this->data['locale'] ?? null;
    }

    public function getQuantity(): ?int
    {
        return $this->data['quantity'] ?? null;
    }

    public function getItems(): ?array
    {
        return $this->data['items'] ?? null;
    }

    public function getStates(): ?array
    {
        return $this->data['states'] ?? null;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        if ($request->attributes->getBoolean('_stateless')) {
            return;
        }

        try {
            /** @var OrderInterface $cart */
            $cart = $this->cartContext->getCart();

            $itemsData = $cart->getItems()->map(static function (OrderItemInterface $item): array {
                $variant = $item->getVariant();
                $product = $variant->getProduct();

                return [
                    'id' => $item->getId(),
                    'variantName' => $variant->getName(),
                    'variantId' => $variant->getId(),
                    'variantCode' => $variant->getCode(),
                    'quantity' => $item->getQuantity(),
                    'productName' => $product->getName(),
                    'productCode' => $product->getCode(),
                    'productId' => $product->getId(),
                ];
            })->toArray();

            $this->data = [
                'id' => $cart->getId(),
                'total' => $cart->getTotal(),
                'subtotal' => $cart->getItemsTotal(),
                'currency' => $cart->getCurrencyCode(),
                'locale' => $cart->getLocaleCode(),
                'quantity' => count($cart->getItems()),
                'items' => $itemsData,
                'states' => [
                    'main' => $cart->getState(),
                    'checkout' => $cart->getCheckoutState(),
                    'shipping' => $cart->getShippingState(),
                    'payment' => $cart->getPaymentState(),
                ],
            ];
        } catch (CartNotFoundException) {
            $this->data = [];
        }
    }

    public function reset(): void
    {
        $this->data = [];
    }

    public function getName(): string
    {
        return 'sylius_cart';
    }
}
