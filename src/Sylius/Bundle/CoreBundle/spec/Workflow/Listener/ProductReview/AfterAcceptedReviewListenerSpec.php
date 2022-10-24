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

namespace spec\Sylius\Bundle\CoreBundle\Workflow\Listener\ProductReview;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Workflow\Callback\ProductReview\AfterAcceptedReviewCallbackInterface;
use Sylius\Bundle\CoreBundle\Workflow\Listener\ProductReview\AfterAcceptedReviewListener;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Workflow\Event\Event;

final class AfterAcceptedReviewListenerSpec extends ObjectBehavior
{
    function let(
        AfterAcceptedReviewCallbackInterface $firstCallback,
        AfterAcceptedReviewCallbackInterface $secondCallback,
    ): void {
        $this->beConstructedWith([$firstCallback->getWrappedObject(), $secondCallback->getWrappedObject()]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AfterAcceptedReviewListener::class);
    }

    function it_calls_every_callbacks(
        Event $event,
        ReviewInterface $productReview,
        AfterAcceptedReviewCallbackInterface $firstCallback,
        AfterAcceptedReviewCallbackInterface $secondCallback,
    ): void {
        $event->getSubject()->willReturn($productReview);

        $firstCallback->call($productReview)->shouldBeCalled();
        $secondCallback->call($productReview)->shouldBeCalled();

        $this->call($event);
    }
}
