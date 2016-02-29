<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Domain;

use Behat\Behat\Context\Context;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class PromotionContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        PromotionRepositoryInterface $promotionRepository,
        RepositoryInterface $couponRepository
    ) {
        $this->beConstructedWith($sharedStorage, $promotionRepository, $couponRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Domain\PromotionContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_removes_a_coupon(RepositoryInterface $couponRepository, CouponInterface $coupon)
    {
        $couponRepository->remove($coupon)->shouldBeCalled();

        $this->iDeleteCoupon($coupon);
    }

    function it_checks_whether_a_coupon_is_not_in_the_registry(
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $couponRepository->find(5)->willReturn(null);

        $this->couponShouldNotExistInTheRegistry($coupon);
    }

    function it_throws_exception_when_a_coupon_is_found_when_it_should_not(
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $couponRepository->find(5)->willReturn($coupon);

        $this
            ->shouldThrow(NotEqualException::class)
            ->during('couponShouldNotExistInTheRegistry', [$coupon])
        ;
    }

    function it_checks_whether_a_coupon_is_in_the_registry(
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $couponRepository->find(5)->willReturn($coupon);

        $this->couponShouldStillExistInTheRegistry($coupon);
    }

    function it_throws_exception_when_a_coupon_is_not_found_when_it_should(
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $couponRepository->find(5)->willReturn(null);

        $this
            ->shouldThrow(FailureException::class)
            ->during('couponShouldStillExistInTheRegistry', [$coupon])
        ;
    }
}
