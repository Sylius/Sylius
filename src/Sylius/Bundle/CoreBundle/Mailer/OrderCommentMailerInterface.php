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
 * @author Myke Hines <myke@webhines.com>
 */
interface OrderCommentMailerInterface
{
    /**
     * @param OrderInterface        $order
     * @param null|CommentInterface $comment
     */
    public function sendOrderComment(OrderInterface $order, CommentInterface $comment = null);
}
