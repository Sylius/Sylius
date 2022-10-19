<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Workflow\Listener\OrderCheckout;

use Sylius\Bundle\CoreBundle\Workflow\Reactor\AfterCompletedCheckout\AfterCompletedCheckoutReactorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterOrderCheckoutCompleteListener
{
    /** @param AfterCompletedCheckoutReactorInterface[] $reactors */
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
