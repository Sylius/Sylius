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

use Sylius\Bundle\CoreBundle\Mailer\OrderConfirmationMailerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sends Order Confirmation email when triggered by event
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class OrderConfirmationEmailListener
{
    /**
     * @var OrderConfirmationMailerInterface
     */
    protected $mailer;

    public function __construct(OrderConfirmationMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function processOrderConfirmation(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $this->mailer->sendOrderConfirmation($order);
    }
}
