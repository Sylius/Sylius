<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SequenceBundle\EventListener;

use Sylius\Bundle\SequenceBundle\Doctrine\ORM\NumberListener as DoctrineNumberListener;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets appropriate order number before saving.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class NumberListener
{
    /**
     * Doctrine number listener.
     *
     * @var DoctrineNumberListener
     */
    protected $listener;

    /**
     * Constructor.
     *
     * @param DoctrineNumberListener $listener
     */
    public function __construct(DoctrineNumberListener $listener)
    {
        $this->listener = $listener;
    }

    /**
     * Use generator to add a proper number to subject entity.
     *
     * @param GenericEvent $event
     */
    public function generateNumber(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if (null !== $subject->getNumber()) {
            return;
        }

        $this->listener->enableEntity($subject);
    }
}
