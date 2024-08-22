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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Common;

use Sylius\Bundle\ShopBundle\Twig\Component\Cart\FormComponent;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Context\CartNotFoundException;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsLiveComponent]
class CartComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(fieldName: 'cart')]
    public ?Order $cart;

    public function __construct(private readonly CartContextInterface $cartContext)
    {
    }

    #[PreMount]
    public function initializeCart(): void
    {
        $this->cart = $this->getCart();
    }

    #[LiveListener(FormComponent::SYLIUS_SHOP_CART_CHANGED)]
    #[LiveListener(FormComponent::SYLIUS_SHOP_CART_CLEARED)]
    public function refreshCart(#[LiveArg] ?Order $cart = null): void
    {
        $this->cart = $cart ?? $this->getCart();
    }

    private function getCart(): ?Order
    {
        try {
            $cart = $this->cartContext->getCart();
        } catch (CartNotFoundException) {
            return null;
        }

        if (!$cart instanceof Order) {
            throw new UnexpectedTypeException($cart, Order::class);
        }

        return $cart;
    }
}
