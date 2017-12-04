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

namespace spec\Sylius\Component\Review\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewSpec extends ObjectBehavior
{
    function it_implements_review_interface(): void
    {
        $this->shouldImplement(ReviewInterface::class);
    }

    function it_has_a_title(): void
    {
        $this->setTitle('review title');
        $this->getTitle()->shouldReturn('review title');
    }

    function it_has_a_rating(): void
    {
        $this->setRating(5);
        $this->getRating()->shouldReturn(5);
    }

    function it_has_a_comment(): void
    {
        $this->setComment('Lorem ipsum dolor');
        $this->getComment()->shouldReturn('Lorem ipsum dolor');
    }

    function it_has_an_author(ReviewerInterface $author): void
    {
        $this->setAuthor($author);
        $this->getAuthor()->shouldReturn($author);
    }

    function it_has_a_status(): void
    {
        $this->getStatus()->shouldReturn(ReviewInterface::STATUS_NEW);
    }

    function it_has_a_review_subject(ReviewableInterface $reviewSubject): void
    {
        $this->setReviewSubject($reviewSubject);
        $this->getReviewSubject()->shouldReturn($reviewSubject);
    }

    function it_has_a_created_at(\DateTime $createdAt): void
    {
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function it_has_an_updated_at(\DateTime $updatedAt): void
    {
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }
}
