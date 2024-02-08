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

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface ReviewInterface extends TimestampableInterface, ResourceInterface
{
    public const STATUS_NEW = 'new';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public function getTitle(): ?string;

    public function setTitle(?string $title): void;

    public function getRating(): ?int;

    public function setRating(?int $rating): void;

    public function getComment(): ?string;

    public function setComment(?string $comment): void;

    public function getAuthor(): ?ReviewerInterface;

    public function setAuthor(?ReviewerInterface $author): void;

    public function getStatus(): ?string;

    public function setStatus(?string $status): void;

    public function getReviewSubject(): ?ReviewableInterface;

    public function setReviewSubject(?ReviewableInterface $reviewSubject): void;
}
