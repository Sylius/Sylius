<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Context\Transform\CouponContext;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin CouponContext
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class CouponContextSpec extends ObjectBehavior
{
    function let(RepositoryInterface $couponRepository)
    {
        $this->beConstructedWith($couponRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Transform\CouponContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_gets_coupon_by_its_code(RepositoryInterface $couponRepository, CouponInterface $coupon)
    {
        $coupon->getCode()->willReturn('BEST-CODE');
        $couponRepository->findOneBy(['code' => 'BEST-CODE'])->willReturn($coupon);

        $this->getCouponByCode('BEST-CODE');
    }

    function it_throws_exception_when_coupon_with_given_code_was_not_found(RepositoryInterface $couponRepository)
    {
        $couponRepository->findOneBy(['code' => 'NO-CODE'])->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('getCouponByCode', ['NO-CODE']);
    }
}
