<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ReviewBundle\Model;

/**
 * Review
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class Review implements ReviewInterface
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var integer
     */
    protected $rating;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $moderationStatus;

    /**
     * @var integer
     */
    protected $guestReviewer;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->moderationStatus = ReviewInterface::MODERATION_STATUS_UNMODERATED;
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
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setRating($rating)
    {
        $this->rating = $rating;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setModerationStatus($moderationStatus)
    {
        $this->moderationStatus = $moderationStatus;
    
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getModerationStatus()
    {
        return $this->moderationStatus;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuestReviewer()
    {
        return $this->guestReviewer;
    }

    /**
     * {@inheritdoc}
     */
    public function setGuestReviewer(GuestReviewerInterface $guestReviewer)
    {
        $this->guestReviewer = $guestReviewer;

        return $this;
    }
}
