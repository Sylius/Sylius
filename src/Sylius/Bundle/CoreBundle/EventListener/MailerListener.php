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
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\Order\Model\CommentInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MailerListener
{
    /**
     * @var SenderInterface
     */
    protected $emailSender;

    /**
     * @param SenderInterface $emailSender
     */
    public function __construct(SenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
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
                CustomerInterface::class
            );
        }

        if (null === ($user = $customer->getUser())) {
            return;
        }

        if (!$user->isEnabled()) {
            return;
        }

        if (null === ($email = $customer->getEmail()) || empty($email)) {
            return;
        }

        $this->emailSender->send(Emails::USER_CONFIRMATION, [$email], ['user' => $user]);
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

        if (!$comment->getNotifyCustomer()) {
            return;
        }

        if (null === $order = $comment->getOrder()) {
            return;
        }

        if (null === $order->getCustomer()) {
            return;
        }

        if (null === ($email = $order->getCustomer()->getEmail()) || empty($email)) {
            return;
        }

        $this->emailSender->send(
            Emails::ORDER_COMMENT,
            [$email],
            [
                'order' => $order,
                'comment' => $comment,
            ]
        );
    }
}
