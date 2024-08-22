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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Checkout;

use Sylius\Bundle\ShopBundle\Twig\Component\Cart\FormComponent;
use Sylius\Component\Core\Model\Order;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class SummaryComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    #[LiveProp(fieldName: 'cart')]
    public Order $cart;

    #[LiveListener(FormComponent::SYLIUS_SHOP_CART_CHANGED)]
    public function refreshCart(#[LiveArg] Order $cart): void
    {
        $this->cart = $cart;
    }
}
