<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout;

use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterOrderCheckoutCompleteListener
{
    public function __construct(private iterable $processors)
    {
    }

    public function process(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        foreach ($this->processors as $processor) {
            $processor->process($order);
        }
    }
}
