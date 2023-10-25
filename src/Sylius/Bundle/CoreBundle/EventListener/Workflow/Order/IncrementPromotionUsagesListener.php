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
use Sylius\Component\Core\Promotion\Modifier\OrderPromotionsUsageModifierInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class IncrementPromotionUsagesListener
{
    public function __construct(private OrderPromotionsUsageModifierInterface $orderPromotionsUsageModifier)
    {
    }

    public function __invoke(CompletedEvent $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->orderPromotionsUsageModifier->increment($order);
    }
}
