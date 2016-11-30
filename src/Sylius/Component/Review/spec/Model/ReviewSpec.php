<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Review\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Model\Review;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ReviewSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Review::class);
    }

    function it_implements_review_interface()
    {
        $this->shouldImplement(ReviewInterface::class);
    }

    function it_has_a_title()
    {
        $this->setTitle('review title');
        $this->getTitle()->shouldReturn('review title');
    }

    function it_has_a_rating()
    {
        $this->setRating(5);
        $this->getRating()->shouldReturn(5);
    }

    function it_has_a_comment()
    {
        $this->setComment('Lorem ipsum dolor');
        $this->getComment()->shouldReturn('Lorem ipsum dolor');
    }

    function it_has_an_author(ReviewerInterface $author)
    {
        $this->setAuthor($author);
        $this->getAuthor()->shouldReturn($author);
    }

    function it_has_a_status()
    {
        $this->getStatus()->shouldReturn(ReviewInterface::STATUS_NEW);
    }

    function it_has_a_review_subject(ReviewableInterface $reviewSubject)
    {
        $this->setReviewSubject($reviewSubject);
        $this->getReviewSubject()->shouldReturn($reviewSubject);
    }

    function it_has_a_created_at(\DateTime $createdAt)
    {
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    function it_has_an_updated_at(\DateTime $updatedAt)
    {
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }
}
