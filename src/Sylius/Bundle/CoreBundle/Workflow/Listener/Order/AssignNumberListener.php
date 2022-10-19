<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\Order;

use Sylius\Bundle\OrderBundle\NumberAssigner\OrderNumberAssignerInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AssignNumberListener
{
    public function __construct(private OrderNumberAssignerInterface $orderNumberAssigner)
    {
    }

    public function assignNumber(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        $this->orderNumberAssigner->assignNumber($order);
    }
}
