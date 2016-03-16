<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Review\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Review\Factory\ReviewFactoryInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ReviewFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RepositoryInterface $subjectRepository)
    {
        $this->beConstructedWith($factory, $subjectRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Review\Factory\ReviewFactory');
    }

    function it_is_a_resource_factory()
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_implements_review_factory_interface()
    {
        $this->shouldImplement(ReviewFactoryInterface::class);
    }

    function it_creates_new_review($factory, ReviewInterface $review)
    {
        $factory->createNew()->willReturn($review);

        $this->createNew()->shouldReturn($review);
    }

    function it_throws_an_exception_when_subject_is_not_found($subjectRepository)
    {
        $subjectRepository->find(20)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('createForSubject', [20])
        ;
    }

    function it_creates_a_review_with_subject(
        $factory,
        $subjectRepository,
        ReviewableInterface $subject,
        ReviewInterface $review
    ) {
        $factory->createNew()->willReturn($review);
        $subjectRepository->find(10)->willReturn($subject);
        $review->setReviewSubject($subject)->shouldBeCalled();

        $this->createForSubject(10)->shouldReturn($review);
    }
}
