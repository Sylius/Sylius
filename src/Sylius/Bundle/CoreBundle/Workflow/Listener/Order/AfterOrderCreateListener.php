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

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Bundle\CoreBundle\Workflow\Reactor\AfterPlacedOrder\AfterPlacedOrderReactorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterOrderCreateListener
{
    /** @param AfterPlacedOrderReactorInterface[] $reactors */
    public function __construct(private iterable $reactors)
    {
    }

    public function process(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        foreach ($this->reactors as $reactor) {
            $reactor->react($order);
        }
    }
}
