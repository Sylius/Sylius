<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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

    /** @var mixed */
    protected $id;

    /** @var string|null */
    protected $title;

    /** @var int|null */
    protected $rating;

    /** @var string|null */
    protected $comment;

    /** @var ReviewerInterface|null */
    protected $author;

    /** @var string */
    protected $status = ReviewInterface::STATUS_NEW;

    /** @var ReviewableInterface|null */
    protected $reviewSubject;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        $this->rating = $rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): void
    {
        $this->comment = $comment;
    }

    public function getAuthor(): ?ReviewerInterface
    {
        return $this->author;
    }

    public function setAuthor(?ReviewerInterface $author): void
    {
        $this->author = $author;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getReviewSubject(): ?ReviewableInterface
    {
        return $this->reviewSubject;
    }

    public function setReviewSubject(?ReviewableInterface $reviewSubject): void
    {
        $this->reviewSubject = $reviewSubject;
    }
}
