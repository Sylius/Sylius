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
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Resource\Model\ResourceInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;

#[AsLiveComponent]
class SummaryComponent
{
    /** @use ResourceLivePropTrait<OrderInterface> */
    use ResourceLivePropTrait;

    #[LiveProp(hydrateWith: 'hydrateResource', dehydrateWith: 'dehydrateResource')]
    public ?ResourceInterface $cart = null;

    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        $this->initialize($orderRepository);
    }

    #[LiveListener(FormComponent::SYLIUS_SHOP_CART_CHANGED)]
    public function refreshCart(#[LiveArg] int $cart): void
    {
        $this->cart = $this->hydrateResource($cart);
    }
}
