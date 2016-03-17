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
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\EventListener\OrderPromotionListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Sylius\Component\Promotion\SyliusPromotionEvents;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Piotr Walków <walkow.piotr@gmail.com>
 */
class OrderPromotionListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session, TranslatorInterface $translator)
    {
        $this->beConstructedWith($session, $translator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderPromotionListener::class);
    }

    function it_adds_success_message_to_flashbag_when_coupon_is_eligible(
        GenericEvent $event,
        FlashBagInterface $flashBag,
        $session,
        $translator
    ) {
        $event->getName()->willReturn(SyliusPromotionEvents::COUPON_ELIGIBLE);

        $translator
            ->trans(Argument::any(), [], 'flashes')
            ->shouldBeCalled();

        $flashBag->add('success', Argument::any())->shouldBeCalled();
        $session->getBag('flashes')->shouldBeCalled()->willReturn($flashBag);

        $this->handleCouponPromotion($event);
    }

    function it_adds_error_message_to_flashbag_when_coupon_is_not_eligible(
        GenericEvent $event,
        FlashBagInterface $flashBag,
        $session,
        $translator
    ) {
        $event->getName()->willReturn(SyliusPromotionEvents::COUPON_NOT_ELIGIBLE);
        $translator->trans(Argument::any(), Argument::any(), 'flashes')
            ->shouldBeCalled();

        $flashBag->add('error', Argument::any())->shouldBeCalled();
        $session->getBag('flashes')->shouldBeCalled()->willReturn($flashBag);

        $this->handleCouponPromotion($event);
    }
}
