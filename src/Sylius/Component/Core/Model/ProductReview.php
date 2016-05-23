<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Like\Model\DislikableInterface;
use Sylius\Component\Like\Model\LikableInterface;
use Sylius\Component\Like\Model\LikeInterface;
use Sylius\Component\Review\Model\Review;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReview extends Review implements LikableInterface, DislikableInterface
{
    /**
     * @var ArrayCollection|LikeInterface[]
     */
    protected $likes;

    /**
     * @var int
     */
    protected $likeCount;

    /**
     * @var int
     */
    protected $dislikeCount;

    /**
     * ProductReview constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->likes = new ArrayCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * {@inheritdoc}
     */
    public function addLike(LikeInterface $like)
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }        
    }

    /**
     * {@inheritdoc}
     */
    public function removeLike(LikeInterface $like)
    {
        $this->likes->remove($like);
    }

    /**
     * {@inheritdoc}
     */
    public function setLikeCount($likeCount)
    {
        $this->likeCount = $likeCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getLikeCount()
    {
        return $this->getLikeCount();
    }

    /**
     * {@inheritdoc}
     */
    public function setDislikeCount($dislikeCount)
    {
        $this->dislikeCount = $dislikeCount;
    }

    /**
     * {@inheritdoc}
     */
    public function getDislikeCount()
    {
        return $this->getDislikeCount();
    }
}
