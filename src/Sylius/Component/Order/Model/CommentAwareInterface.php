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
     * Return order comments.
     *
     * @return Collection|CommentInterface[]
     */
    public function getComments();

    /**
     * Add comment.
     *
     * @param CommentInterface $comment
     *
     * @return $this
     */
    public function addComment(CommentInterface $comment);

    /**
     * Remove comment.
     *
     * @param CommentInterface $comment
     *
     * @return $this
     */
    public function removeComment(CommentInterface $comment);
}
