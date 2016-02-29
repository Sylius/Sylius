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
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Exception\Example\NotEqualException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Behat\Context\Domain\PromotionContext;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

/**
 * @mixin PromotionContext
 *
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

    function it_removes_a_coupon(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $sharedStorage->set('coupon_id', 5)->shouldBeCalled();
        $couponRepository->remove($coupon)->shouldBeCalled();

        $this->iDeleteCoupon($coupon);
    }

    function it_throws_exception_when_there_was_a_problem_with_removing_a_coupon(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $sharedStorage->set('coupon_id', 5)->shouldBeCalled();
        $couponRepository->remove($coupon)->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(ForeignKeyConstraintViolationException::class)->during('iDeleteCoupon', [$coupon]);
    }

    function it_tries_to_remove_a_coupon_and_catches_exception_in_case_of_failure(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $couponRepository->remove($coupon)->willThrow(ForeignKeyConstraintViolationException::class);
        $sharedStorage->set('last_exception', Argument::type(ForeignKeyConstraintViolationException::class))->shouldBeCalled();

        $this->iTryToDeleteCoupon($coupon);
    }

    function it_throws_exception_while_trying_to_remove_a_coupon_and_actually_removing_it(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $couponRepository->remove($coupon)->shouldBeCalled();
        $sharedStorage->set('last_exception', Argument::any())->shouldNotBeCalled();

        $this->shouldThrow(\Exception::class)->during('iTryToDeleteCoupon', [$coupon]);
    }

    function it_checks_whether_a_coupon_is_not_in_the_registry(
        RepositoryInterface $couponRepository
    ) {
        $couponRepository->find(5)->willReturn(null);

        $this->couponShouldNotExistInTheRegistry(5);
    }

    function it_throws_exception_when_a_coupon_is_found_but_it_should_not_exist(
        SharedStorageInterface $sharedStorage,
        RepositoryInterface $couponRepository,
        CouponInterface $coupon
    ) {
        $coupon->getId()->willReturn(5);
        $sharedStorage->get('coupon_id')->willReturn(5);
        $couponRepository->find(5)->willReturn($coupon);

        $this
            ->shouldThrow(NotEqualException::class)
            ->during('couponShouldNotExistInTheRegistry', [5])
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

    function it_throws_exception_when_a_coupon_is_not_found_but_it_should_exist(
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

    function it_removes_a_promotion(PromotionRepositoryInterface $promotionRepository, PromotionInterface $promotion)
    {
        $promotion->getId()->willReturn(5);
        $promotionRepository->find(5)->willReturn($promotion);
        $promotionRepository->remove($promotion)->shouldBeCalled();

        $this->iDeletePromotion($promotion);
    }

    function it_checks_whether_a_promotion_does_not_exist_in_the_registry(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion
    ) {
        $promotion->getId()->willReturn(5);
        $promotionRepository->find(5)->willReturn(null);

        $this->promotionShouldNotExistInTheRegistry($promotion);
    }

    function it_throws_exception_when_a_promotion_is_found_when_it_should_not(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion
    ) {
        $promotion->getId()->willReturn(5);
        $promotionRepository->find(5)->willReturn($promotion);

        $this
            ->shouldThrow(NotEqualException::class)
            ->during('promotionShouldNotExistInTheRegistry', [$promotion])
        ;
    }

    function it_checks_whether_a_promotion_exists_in_the_registry(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion
    ) {
        $promotion->getId()->willReturn(5);
        $promotionRepository->find(5)->willReturn($promotion);

        $this->promotionShouldStillExistInTheRegistry($promotion);
    }

    function it_throws_exception_when_a_promotion_is_not_found_when_it_should(
        PromotionRepositoryInterface $promotionRepository,
        PromotionInterface $promotion
    ) {
        $promotion->getId()->willReturn(5);
        $promotionRepository->find(5)->willReturn(null);

        $this
            ->shouldThrow(FailureException::class)
            ->during('promotionShouldStillExistInTheRegistry', [$promotion])
        ;
    }
}
