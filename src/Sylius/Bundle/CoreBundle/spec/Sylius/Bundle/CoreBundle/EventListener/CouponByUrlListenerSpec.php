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
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sylius\Bundle\CartBundle\Event\CartEvent;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Component\Promotion\Model\Coupon;

class CouponByUrlListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\EventListener\CouponByUrlListener');
    }

    function let(RepositoryInterface $couponRepository, SessionInterface $session)
    {
        $this->beConstructedWith($couponRepository, $session, 'promotionCoupon');
    }

    function it_stores_coupon_in_session_when_in_parameter(Request $request, GetResponseEvent $event, RepositoryInterface $couponRepository, Coupon $coupon, ParameterBag $query, SessionInterface $session)
    {
        $code = 'D0001';
        $coupon->getCode()->willReturn($code);        

        $query->get('promotionCoupon')->willReturn($code);
        $request->query = $query;
        $event->getRequest()->willReturn($request);

        $session->set('coupon_store_promotionCoupon', $code)->shouldBeCalled()->willReturn($code);

        $this->applyRequestToSessionCoupon($event);
    }

}
