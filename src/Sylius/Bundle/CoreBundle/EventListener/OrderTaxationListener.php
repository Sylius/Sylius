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

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderProcessing\TaxationProcessorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Order taxation listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderTaxationListener
{
    /**
     * Order taxation processor.
     *
     * @var TaxationProcessorInterface
     */
    protected $taxationProcessor;

    /**
     * Constructor.
     *
     * @param TaxationProcessorInterface $taxationProcessor
     */
    public function __construct(TaxationProcessorInterface $taxationProcessor)
    {
        $this->taxationProcessor = $taxationProcessor;
    }

    /**
     * Get the order from event and run the taxation processor on it.
     *
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function applyTaxes(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $this->taxationProcessor->applyTaxes($order);

        $order->calculateTotal();
    }
}
