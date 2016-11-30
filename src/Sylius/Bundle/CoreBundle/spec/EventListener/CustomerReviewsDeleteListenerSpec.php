<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\EventListener\CustomerReviewsDeleteListener;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemover;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class CustomerReviewsDeleteListenerSpec extends ObjectBehavior
{
    function let(ReviewerReviewsRemover $reviewerReviewsRemover)
    {
        $this->beConstructedWith($reviewerReviewsRemover);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CustomerReviewsDeleteListener::class);
    }

    function it_removes_soft_deleted_customer_reviews_and_recalculates_their_product_ratings(
        $reviewerReviewsRemover,
        GenericEvent $event,
        ReviewerInterface $author
    ) {
        $event->getSubject()->willReturn($author);
        $reviewerReviewsRemover->removeReviewerReviews($author)->shouldBeCalled();

        $this->removeCustomerReviews($event);
    }

    function it_throws_exception_if_event_subject_is_not_customer_object(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', ReviewerInterface::class))->during('removeCustomerReviews', [$event]);
    }
}
