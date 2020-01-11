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

namespace spec\Sylius\Component\Review\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

final class ReviewFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory): void
    {
        $this->beConstructedWith($factory);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_review_factory_interface(): void
    {
        $this->shouldImplement(ReviewFactoryInterface::class);
    }

    function it_creates_a_new_review(FactoryInterface $factory, ReviewInterface $review): void
    {
        $factory->createNew()->willReturn($review);

        $this->createNew()->shouldReturn($review);
    }

    function it_creates_a_review_with_subject(
        FactoryInterface $factory,
        ReviewableInterface $subject,
        ReviewInterface $review
    ): void {
        $factory->createNew()->willReturn($review);
        $review->setReviewSubject($subject)->shouldBeCalled();

        $this->createForSubject($subject)->shouldReturn($review);
    }

    function it_creates_a_review_with_subject_and_reviewer(
        FactoryInterface $factory,
        ReviewableInterface $subject,
        ReviewInterface $review,
        ReviewerInterface $reviewer
    ): void {
        $factory->createNew()->willReturn($review);
        $review->setReviewSubject($subject)->shouldBeCalled();
        $review->setAuthor($reviewer)->shouldBeCalled();

        $this->createForSubjectWithReviewer($subject, $reviewer);
    }
}
