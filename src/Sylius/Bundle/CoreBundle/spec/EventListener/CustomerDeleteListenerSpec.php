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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Calculator\AverageRatingCalculatorInterface;
use Sylius\Component\Review\Model\ReviewableInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CustomerDeleteListenerSpec extends ObjectBehavior
{
    function let(EntityRepository $reviewRepository, ObjectManager $reviewManager, AverageRatingCalculatorInterface $averageRatingCalculator)
    {
        $this->beConstructedWith($reviewRepository, $reviewManager, $averageRatingCalculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CustomerDeleteListener');
    }

    function it_removes_soft_deleted_customer_reviews_and_recalculates_their_product_ratings(
        $averageRatingCalculator,
        $reviewRepository,
        $reviewManager,
        GenericEvent $event,
        CustomerInterface $author,
        ReviewableInterface $reviewSubject,
        ReviewInterface $review
    ) {
        $event->getSubject()->willReturn($author)->shouldBeCalled();

        $reviewRepository->findBy(array('author' => $author))->willReturn(array($review))->shouldBeCalled();
        $review->getReviewSubject()->willReturn($reviewSubject)->shouldBeCalled();

        $reviewManager->remove($review)->shouldBeCalled();
        $reviewManager->flush()->shouldBeCalled();

        $averageRatingCalculator->calculate($reviewSubject)->willReturn(0)->shouldBeCalled();

        $reviewSubject->setAverageRating(0)->shouldBeCalled();

        $this->removeCustomerReviews($event);
    }

    function it_throws_exception_if_event_subject_is_not_customer_object(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Core\Model\CustomerInterface'))->during('removeCustomerReviews', array($event));
    }
}
