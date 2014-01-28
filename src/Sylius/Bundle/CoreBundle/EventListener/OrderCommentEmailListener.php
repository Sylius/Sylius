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

use Sylius\Bundle\CoreBundle\Mailer\OrderUpdateMailerInterface;
use Sylius\Component\Order\Model\CommentInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Sends order comment email when triggered by event.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class OrderCommentEmailListener
{
    /**
     * @var OrderUpdateMailerInterface
     */
    protected $mailer;

    public function __construct(OrderUpdateMailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param GenericEvent $event
     *
     * @throws UnexpectedTypeException
     */
    public function processOrderComment(GenericEvent $event)
    {
        $comment = $event->getSubject();

        if (!$comment instanceof CommentInterface) {
            throw new UnexpectedTypeException(
                $comment,
                'Sylius\Component\Order\Model\CommentInterface'
            );
        }

        // Trigger notification?
        if ($comment->getNotifyCustomer()) {
            $this->mailer->sendOrderUpdate($comment->getOrder(), $comment);
        }
    }
}
