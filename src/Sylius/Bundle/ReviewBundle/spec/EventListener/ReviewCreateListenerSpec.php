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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface;
use Sylius\Component\Review\Model\Reviewable;
use Sylius\Component\Review\Model\ReviewInterface;
use Sylius\Component\User\Context\CustomerContextInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ReviewCreateListenerSpec extends ObjectBehavior
{
    function let(
        AverageRatingCalculatorInterface $averageRatingCalculator,
        CustomerContextInterface $customerContext,
        ObjectManager $productManager
    ) {
        $this->beConstructedWith($averageRatingCalculator, $customerContext, $productManager);
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

        $this->controlReviewAuthor($event);
    }

    function it_throws_exception_if_event_object_is_not_review_while_controlling_author(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Review\Model\ReviewInterface'))->during('controlReviewAuthor', array($event));
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

        $this->controlReviewAuthor($event)->shouldReturn(null);
    }

    function it_calculates_average_rating_for_newly_added_review_product(
        $averageRatingCalculator,
        $productManager,
        GenericEvent $event,
        ReviewInterface $review,
        Reviewable $reviewSubject
    ) {
        $event->getSubject()->willReturn($review)->shouldBeCalled();
        $review->getProduct()->willReturn($reviewSubject)->shouldBeCalled();

        $averageRatingCalculator->calculate($reviewSubject)->willReturn(4.5)->shouldBeCalled();

        $reviewSubject->setAverageRating(4.5)->shouldBeCalled();
        $productManager->flush($reviewSubject)->shouldBeCalled();

        $this->calculateProductAverageRating($event);
    }

    function it_throws_exception_if_event_object_is_not_review_while_calculating_average_product_rating(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Review\Model\ReviewInterface'))->during('calculateProductAverageRating', array($event));
    }
}
