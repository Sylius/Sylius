<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\Collection;

interface CommentAwareInterface
{
    /**
     * @return Collection|CommentInterface[]
     */
    public function getComments();

    /**
     * @param CommentInterface $comment
     */
    public function addComment(CommentInterface $comment);

    /**
     * @param CommentInterface $comment
     */
    public function removeComment(CommentInterface $comment);
}
