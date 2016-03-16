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
use Sylius\Component\Core\Taxation\OrderTaxesApplicatorInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderTaxationListener
{
    /**
     * @var OrderTaxesApplicatorInterface
     */
    protected $orderTaxesApplicator;

    /**
     * @param OrderTaxesApplicatorInterface $orderTaxesApplicator
     */
    public function __construct(OrderTaxesApplicatorInterface $orderTaxesApplicator)
    {
        $this->orderTaxesApplicator = $orderTaxesApplicator;
    }

    /**
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
                OrderInterface::class
            );
        }

        $this->orderTaxesApplicator->apply($order);
    }
}
