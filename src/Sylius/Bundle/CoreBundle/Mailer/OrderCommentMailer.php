<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\CommentInterface;

/**
 * OrderCommentMailer implementation
 *
 * @author Myke Hines <myke@webhines.com>
 */
class OrderCommentMailer extends AbstractMailer implements OrderCommentMailerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sendOrderComment(OrderInterface $order, CommentInterface $comment = null)
    {
        if ($user = $order->getUser()) {
            $email = $user->getEmail();
        } else {
            $email = $order->getEmail();
        }

        if (!$email) {
            throw new \InvalidArgumentException('Order has to belong to a User.');
        }

        $this->sendEmail(array('order' => $order, 'comment' => $comment), $email);
    }
}
