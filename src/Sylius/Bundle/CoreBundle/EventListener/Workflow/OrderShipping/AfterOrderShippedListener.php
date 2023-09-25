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

namespace Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderShipping;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Webmozart\Assert\Assert;

final class AfterOrderShippedListener
{
    public function __construct(private StateResolverInterface $orderStateResolver)
    {
    }

    public function onOrderShippingCompleted(CompletedEvent $event): void
    {
        Assert::isInstanceOf($event->getSubject(), OrderInterface::class);

        /** @var OrderInterface $order */
        $order = $event->getSubject();

        $this->orderStateResolver->resolve($order);
    }
}
