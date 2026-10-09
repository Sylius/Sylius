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

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Remover\UnusedPromotionCouponRemoverInterface;
use Symfony\Component\Workflow\Event\TransitionEvent;
use Webmozart\Assert\Assert;

final class RemoveUnusedPromotionCouponListener
{
    public function __construct(private UnusedPromotionCouponRemoverInterface $unusedPromotionCouponRemover)
    {
    }

    public function __invoke(TransitionEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->unusedPromotionCouponRemover->remove($order);
    }
}
