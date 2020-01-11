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
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Customer\Context\CustomerContextInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ReviewCreateListenerSpec extends ObjectBehavior
{
    function let(CustomerContextInterface $customerContext): void
    {
        $this->beConstructedWith($customerContext);
    }

    function it_adds_currently_logged_customer_as_author_to_newly_created_review_if_it_has_no_author_yet(
        CustomerContextInterface $customerContext,
        CustomerInterface $customer,
        GenericEvent $event,
        ReviewInterface $review
    ): void {
        $event->getSubject()->willReturn($review);
        $customerContext->getCustomer()->willReturn($customer);
        $review->getAuthor()->willReturn(null);

        $review->setAuthor($customer)->shouldBeCalled();

        $this->ensureReviewHasAuthor($event);
    }

    function it_throws_exception_if_event_object_is_not_review_while_controlling_author(GenericEvent $event): void
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('ensureReviewHasAuthor', [$event])
        ;
    }

    function it_does_nothing_if_review_already_has_author(
        CustomerContextInterface $customerContext,
        CustomerInterface $existingAuthor,
        GenericEvent $event,
        ReviewInterface $review
    ): void {
        $event->getSubject()->willReturn($review);
        $review->getAuthor()->willReturn($existingAuthor);

        $customerContext->getCustomer()->shouldNotBeCalled();

        $this->ensureReviewHasAuthor($event);
    }
}
