<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\OrderBundle\EventListener;

use Sylius\Component\Sequence\Number\GeneratorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets appropriate order number before saving.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class OrderNumberListener
{
    /**
     * Order number generator.
     *
     * @var GeneratorInterface
     */
    protected $generator;

    /**
     * Constructor.
     *
     * @param GeneratorInterface $generator
     */
    public function __construct(GeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Use generator to add a proper number to order.
     *
     * @param GenericEvent $event
     */
    public function generateOrderNumber(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (null !== $order->getNumber()) {
            return;
        }

        $this->generator->generate($order);
    }
}
