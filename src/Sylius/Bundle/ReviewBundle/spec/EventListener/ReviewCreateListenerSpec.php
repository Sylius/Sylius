<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\User\Context\CustomerContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewCreateListenerSpec extends ObjectBehavior
{
    function let(CustomerContextInterface $customerContext)
    {
        $this->beConstructedWith($customerContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\EventListener\ReviewCreateListener');
    }

    function it_adds_currently_logged_customer_as_author_to_newly_created_review_if_it_has_no_author_yet(
        $customerContext,
        CustomerInterface $customer,
        GenericEvent $event,
        ReviewInterface $review
    ) {
        $event->getSubject()->willReturn($review)->shouldBeCalled();
        $customerContext->getCustomer()->willReturn($customer)->shouldBeCalled();
        $review->getAuthor()->willReturn(null);

        $review->setAuthor($customer)->shouldBeCalled();

        $this->ensureReviewHasAuthor($event);
    }

    function it_throws_exception_if_event_object_is_not_review_while_controlling_author(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Review\Model\ReviewInterface'))->during('ensureReviewHasAuthor', array($event));
    }

    function it_does_nothing_if_review_already_has_author(
        $customerContext,
        CustomerInterface $existingAuthor,
        GenericEvent $event,
        ReviewInterface $review
    ) {
        $event->getSubject()->willReturn($review)->shouldBeCalled();
        $review->getAuthor()->willReturn($existingAuthor)->shouldBeCalled();

        $customerContext->getCustomer()->shouldNotBeCalled();

        $this->ensureReviewHasAuthor($event);
    }
}
