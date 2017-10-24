<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Review\Model;

use Sylius\Component\Resource\Model\TimestampableTrait;

class Review implements ReviewInterface
{
    use TimestampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $rating;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var ReviewerInterface
     */
    protected $author;

    /**
     * @var string
     */
    protected $status = ReviewInterface::STATUS_NEW;

    /**
     * @var ReviewableInterface
     */
    protected $reviewSubject;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * {@inheritdoc}
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * {@inheritdoc}
     */
    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    /**
     * {@inheritdoc}
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * {@inheritdoc}
     */
    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthor(): ?ReviewerInterface
    {
        return $this->author;
    }

    /**
     * {@inheritdoc}
     */
    public function setAuthor(?ReviewerInterface $author): void
    {
        $this->author = $author;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * {@inheritdoc}
     */
    public function getReviewSubject(): ?ReviewableInterface
    {
        return $this->reviewSubject;
    }

    /**
     * {@inheritdoc}
     */
    public function setReviewSubject(?ReviewableInterface $reviewSubject): void
    {
        $this->reviewSubject = $reviewSubject;
    }
}
