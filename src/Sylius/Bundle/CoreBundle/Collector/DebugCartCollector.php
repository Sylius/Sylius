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

namespace Sylius\Bundle\CoreBundle\Collector;

use Sylius\Bundle\CoreBundle\Application\Kernel;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

final class DebugCartCollector extends DataCollector
{
    /** @var CartContextInterface */
    private $cartContext;

    public function __construct(
        CartContextInterface $cartContext
    ) {
        $this->cartContext = $cartContext;
        $cart = $cartContext->getCart();

        $items = [];
        foreach ($cart->getItems() as $item) {
            $variant = $item->getVariant();
            $product = $variant->getProduct();

            $items[] = [
                'id' => $item->getId(),
                'variantName' => $variant->getName(),
                'variantId' => $variant->getId(),
                'variantCode' => $variant->getCode(),
                'quantity' => $item->getQuantity(),
                'productName' => $product->getName(),
                'productId' => $product->getId(),
            ];
        }

        $this->data = [
            'cart_id' => $cart->getId(),
            'total' => $cart->getTotal(),
            'subtotal' => $cart->getItemsTotal(),
            'currency' => $cart->getCurrencyCode(),
            'locale' => $cart->getLocaleCode(),
            'quantity' => count($cart->getItems()),
            'items' => $items,
            'states' => [
                'main' => $cart->getState(),
                'checkout' => $cart->getCheckoutState(),
                'shipping' => $cart->getShippingState(),
                'payment' => $cart->getPaymentState(),
            ]
        ];
    }

    public function getCartId(): ?int
    {
        return $this->data['cart_id'];
    }

    public function getTotal(): ?int
    {
        return $this->data['total'];
    }

    public function getSubtotal(): ?int
    {
        return $this->data['subtotal'];
    }

    public function getCurrency(): ?string
    {
        return $this->data['currency'];
    }

    public function getLocale(): ?string
    {
        return $this->data['locale'];
    }

    public function getQuantity(): ?int
    {
        return $this->data['quantity'];
    }

    public function getItems(): ?array
    {
        return $this->data['items'];
    }

    public function getStates(): ?array
    {
        return $this->data['states'];
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Request $request, Response $response, \Exception $exception = null): void
    {
    }

    /**
     * {@inheritdoc}
     */
    public function reset(): void
    {
        $this->data['cart_id'] = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_core_debug_cart';
    }
}
