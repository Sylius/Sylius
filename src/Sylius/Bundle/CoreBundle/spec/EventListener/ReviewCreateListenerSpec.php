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
use Sylius\Bundle\CoreBundle\EventListener\ReviewCreateListener;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\Review\Model\ReviewerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ReviewCreateListenerSpec extends ObjectBehavior
{
    function let(CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ReviewCreateListener::class);
    }

    function it_adds_currently_logged_customer_as_author_to_newly_created_review_if_it_has_no_author_yet(
        $customerContext,
        ReviewerInterface $customer,
        GenericEvent $event,
        ReviewInterface $review
    ) {
        $event->getSubject()->willReturn($review);
        $customerContext->getCustomer()->willReturn($customer);
        $review->getAuthor()->willReturn(null);

        $review->setAuthor($customer)->shouldBeCalled();

        $this->ensureReviewHasAuthor($event);
    }

    function it_throws_exception_if_event_object_is_not_review_while_controlling_author(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(new UnexpectedTypeException('badObject', ReviewInterface::class))
            ->during('ensureReviewHasAuthor', [$event])
        ;
    }

    function it_does_nothing_if_review_already_has_author(
        $customerContext,
        ReviewerInterface $existingAuthor,
        GenericEvent $event,
        ReviewInterface $review
    ) {
        $event->getSubject()->willReturn($review);
        $review->getAuthor()->willReturn($existingAuthor);

        $customerContext->getCustomer()->shouldNotBeCalled();

        $this->ensureReviewHasAuthor($event);
    }
}
