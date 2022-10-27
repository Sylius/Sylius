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

use Sylius\Bundle\CoreBundle\Workflow\Callback\Order\AfterCanceledOrderCallbackInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\Workflow\Event\Event;
use Webmozart\Assert\Assert;

final class AfterCanceledOrderListener
{
    /** @param AfterCanceledOrderCallbackInterface[] $callbacks */
    public function __construct(private iterable $callbacks)
    {
        Assert::allIsInstanceOf($callbacks, AfterCanceledOrderCallbackInterface::class);
    }

    public function call(Event $event): void
    {
        /** @var OrderInterface $order */
        $order = $event->getSubject();

        foreach ($this->callbacks as $callback) {
            $callback->call($order);
        }
    }
}
