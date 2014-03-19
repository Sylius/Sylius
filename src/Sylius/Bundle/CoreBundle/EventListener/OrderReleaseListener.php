<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Sylius\Bundle\CoreBundle\Releaser\ReleaserInterface;

/**
 * Order release listener.
 *
 * @author Foo Pang <foo.pang@gmail.com>
 */
class OrderReleaseListener
{
    /**
     * Expired orders releaser.
     *
     * @var ReleaserInterface
     */
    protected $releaser;

    /**
     * Constructor.
     *
     * @param ReleaserInterface $releaser
     */
    public function __construct(ReleaserInterface $releaser)
    {
        $this->releaser = $releaser;
    }

    /**
     * Get the order from event and release the order.
     *
     * @param GenericEvent $event
     *
     * @throws \InvalidArgumentException
     */
    public function releaseOrder(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new \InvalidArgumentException(
                'Order inventory listener requires event subject to be instance of "Sylius\Bundle\CoreBundle\Model\OrderItemInterface"'
            );
        }

        $this->releaser->release($order);
    }
}
