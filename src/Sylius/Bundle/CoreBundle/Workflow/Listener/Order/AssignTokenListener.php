<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Symfony\Component\Workflow\Event\Event;

final class AssignTokenListener
{
    public function __construct(private OrderTokenAssignerInterface $orderTokenAssigner)
    {
    }

    public function assignToken(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        $this->orderTokenAssigner->assignTokenValue($order);
    }
}
