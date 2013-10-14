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

use Sylius\Bundle\OrderBundle\Generator\OrderNumberGeneratorInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sets appropriate order number before saving.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderNumberListener
{
    /**
     * Order number generator.
     *
     * @var OrderNumberGeneratorInterface
     */
    protected $generator;

    /**
     * Constructor.
     *
     * @param OrderNumberGeneratorInterface $generator
     */
    public function __construct(OrderNumberGeneratorInterface $generator)
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
        $this->generator->generate($event->getSubject());
    }
}
