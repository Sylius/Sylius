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
use Sylius\Component\Core\Model\Product;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductDeleteListenerSpec extends ObjectBehavior
{
    function let(ObjectManager $reviewManager)
    {
        $this->beConstructedWith($reviewManager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\ProductDeleteListener');
    }

    function it_removes_soft_deleted_product_reviews(
        $reviewManager,
        GenericEvent $event,
        Product $product,
        ReviewInterface $review
    ) {
        $event->getSubject()->willReturn($product)->shouldBeCalled();

        $product->getReviews()->willReturn(array($review))->shouldBeCalled();
        $product->setAverageRating(null)->shouldBeCalled();

        $reviewManager->remove($review)->shouldBeCalled();
        $reviewManager->flush()->shouldBeCalled();

        $this->removeProductReviews($event);
    }

    function it_throws_exception_if_event_subject_is_not_product_object(GenericEvent $event)
    {
        $event->getSubject()->willReturn('badObject')->shouldBeCalled();

        $this->shouldThrow(new UnexpectedTypeException('badObject', 'Sylius\Component\Core\Model\ProductInterface'))->during('removeProductReviews', array($event));
    }
}
