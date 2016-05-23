<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Like\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @author Loïc Frémont <loic@mobizel.com>
 */
class Like implements LikeInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $authorLike;

    /**
     * @var LikerInterface
     */
    protected $author;

    /**
     * @var LikableInterface
     */
    protected $likeSubject;

    /**
     * Like constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->authorLike = true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function isAuthorLike()
    {
        return $this->authorLike;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthorLike($authorLike)
    {
        $this->authorLike = $authorLike;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor(LikerInterface $author = null)
    {
        $this->author = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function getLikeSubject()
    {
        return $this->likeSubject;
    }

    /**
     * {@inheritdoc}
     */
    public function setLikeSubject(LikableInterface $likeSubject)
    {
        $this->likeSubject = $likeSubject;
    }
}
