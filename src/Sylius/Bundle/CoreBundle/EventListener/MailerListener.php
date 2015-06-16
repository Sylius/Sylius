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

use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Order\Model\CommentInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Generic mailer listener.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MailerListener
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function sendOrderConfirmationEmail(GenericEvent $event)
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface) {
            throw new UnexpectedTypeException(
                $order,
                'Sylius\Component\Core\Model\OrderInterface'
            );
        }

        $this->emailSender->send(Emails::ORDER_CONFIRMATION, array($order->getCustomer()->getEmail()), array('order' => $order));
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function sendShipmentConfirmationEmail(GenericEvent $event)
    {
        $shipment = $event->getSubject();

        if (!$shipment instanceof ShipmentInterface) {
            throw new UnexpectedTypeException(
                $shipment,
                'Sylius\Component\Shipping\Model\ShipmentInterface'
            );
        }

        /** @var OrderInterface $order */
        $order = $shipment->getOrder();
        $this->emailSender->send(Emails::SHIPMENT_CONFIRMATION, array($order->getCustomer()->getEmail()), array(
            'shipment' => $shipment,
            'order' => $order
        ));
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function sendUserConfirmationEmail(GenericEvent $event)
    {
        $customer = $event->getSubject();

        if (!$customer instanceof CustomerInterface) {
            throw new UnexpectedTypeException(
                $customer,
                'Sylius\Component\Core\Model\CustomerInterface'
            );
        }

        if (null === $user = $customer->getUser()) {
            return;
        }

        if (!$user->isEnabled()) {
            return;
        }
        $this->emailSender->send(Emails::USER_CONFIRMATION, array($customer->getEmail()), array('user' => $user));
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function sendOrderCommentEmail(GenericEvent $event)
    {
        $comment = $event->getSubject();

        if (!$comment instanceof CommentInterface) {
            throw new UnexpectedTypeException(
                $comment,
                'Sylius\Component\Order\Model\CommentInterface'
            );
        }

        if ($comment->getNotifyCustomer()) {
            $order = $comment->getOrder();
            $email = $order->getCustomer()->getEmail();

            $this->emailSender->send(Emails::ORDER_COMMENT, array($email), array(
                'order'   => $order,
                'comment' => $comment,
            ));
        }
    }
}
