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

namespace spec\Sylius\Bundle\CoreBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Remover\ReviewerReviewsRemoverInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class CustomerReviewsDeleteListenerSpec extends ObjectBehavior
{
    function let(ReviewerReviewsRemoverInterface $reviewerReviewsRemover): void
    {
        $this->beConstructedWith($reviewerReviewsRemover);
    }

    function it_removes_soft_deleted_customer_reviews_and_recalculates_their_product_ratings(
        ReviewerReviewsRemoverInterface $reviewerReviewsRemover,
        GenericEvent $event,
        ReviewerInterface $author
    ): void {
        $event->getSubject()->willReturn($author);
        $reviewerReviewsRemover->removeReviewerReviews($author)->shouldBeCalled();

        $this->removeCustomerReviews($event);
    }

    function it_throws_exception_if_event_subject_is_not_customer_object(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('removeCustomerReviews', [$event]);
    }
}
