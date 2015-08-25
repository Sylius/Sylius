<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Review\Model;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
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
     * @var ReviewAuthorInterface
     */
    protected $author;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var ReviewableInterface
     */
    protected $reviewSubject;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->status = ReviewInterface::STATUS_NEW;
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
    public function setAuthor(ReviewAuthorInterface $author = null)
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
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewSubject()
    {
        return $this->reviewSubject;
    }

    /**
     * {@inheritdoc}
     */
    public function setReviewSubject(ReviewableInterface $reviewSubject)
    {
        $this->reviewSubject = $reviewSubject;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
