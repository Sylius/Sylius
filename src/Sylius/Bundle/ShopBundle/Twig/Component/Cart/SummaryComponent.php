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

namespace Sylius\Bundle\ShopBundle\Twig\Component\Cart;

use Sylius\Bundle\UiBundle\Twig\Component\ResourceLivePropTrait;
use Sylius\Bundle\UiBundle\Twig\Component\TemplatePropTrait;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
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

    /** @use ResourceLivePropTrait<OrderInterface> */
    use ResourceLivePropTrait;

    use TemplatePropTrait;

    #[LiveProp(hydrateWith: 'hydrateResource', dehydrateWith: 'dehydrateResource')]
    public ?ResourceInterface $cart = null;

    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->initialize($orderRepository);
    }

    #[LiveListener(FormComponent::SYLIUS_SHOP_CART_CHANGED)]
    public function refreshCart(#[LiveArg] mixed $cartId): void
    {
        $this->cart = $this->hydrateResource($cartId);
    }
}
