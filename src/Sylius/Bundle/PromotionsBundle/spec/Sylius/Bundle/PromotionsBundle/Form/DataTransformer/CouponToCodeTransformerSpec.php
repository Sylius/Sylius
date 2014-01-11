<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PromotionsBundle\Form\DataTransformer;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CouponToCodeTransformerSpec extends ObjectBehavior
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectRepository      $couponRepository
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $dispatcher
     */
    function let($couponRepository, $dispatcher)
    {
        $this->beConstructedWith($couponRepository, $dispatcher);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PromotionsBundle\Form\DataTransformer\CouponToCodeTransformer');
    }

    function it_should_return_empty_string_if_null_transormed()
    {
        $this->transform(null)->shouldReturn('');
    }

    function it_should_complain_if_not_Sylius_coupon_transformed()
    {
        $this
            ->shouldThrow('Symfony\Component\Form\Exception\UnexpectedTypeException')
            ->duringTransform(new \stdClass())
        ;
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponInterface $coupon
     */
    function it_should_transform_coupon_into_its_code($coupon)
    {
        $coupon->getCode()->willReturn('C123');

        $this->transform($coupon)->shouldReturn('C123');
    }

    function it_should_return_null_if_empty_string_reverse_transformed()
    {
        $this->reverseTransform('')->shouldReturn(null);
    }

    function it_should_return_null_if_coupon_not_found_on_reverse_transform($couponRepository)
    {
        $couponRepository
            ->findOneBy(array('code' => 'FREEIPHONE5'))
            ->shouldBeCalled()
            ->willReturn(null)
        ;

        $this->reverseTransform('FREEIPHONE5')->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\PromotionsBundle\Model\CouponInterface $coupon
     */
    function it_should_return_coupon_if_found_on_reverse_transform($couponRepository, $coupon)
    {
        $couponRepository
            ->findOneBy(array('code' => 'FREEIPHONE5'))
            ->shouldBeCalled()
            ->willReturn($coupon)
        ;

        $coupon->isValid()->shouldBeCalled()->willReturn(true);

        $this->reverseTransform('FREEIPHONE5')->shouldReturn($coupon);
    }
}
