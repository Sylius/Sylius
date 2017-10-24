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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ReviewInterface extends TimestampableInterface, ResourceInterface
{
    public const STATUS_NEW = 'new';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_REJECTED = 'rejected';

    /**
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void;

    /**
     * @return int|null
     */
    public function getRating(): ?int;

    /**
     * @param int|null $rating
     */
    public function setRating(?int $rating): void;

    /**
     * @return string|null
     */
    public function getComment(): ?string;

    /**
     * @param string|null $comment
     */
    public function setComment(?string $comment): void;

    /**
     * @return ReviewerInterface|null
     */
    public function getAuthor(): ?ReviewerInterface;

    /**
     * @param ReviewerInterface|null $author
     */
    public function setAuthor(?ReviewerInterface $author): void;

    /**
     * @return string|null
     */
    public function getStatus(): ?string;

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void;

    /**
     * @return ReviewableInterface|null
     */
    public function getReviewSubject(): ?ReviewableInterface;

    /**
     * @param ReviewableInterface|null $reviewSubject
     */
    public function setReviewSubject(?ReviewableInterface $reviewSubject): void;
}
