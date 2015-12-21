<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Promotion\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\FilterCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Generator\CouponGeneratorInterface;
use Sylius\Component\Promotion\Generator\Instruction;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGeneratorSpec extends ObjectBehavior
{
    function let(FactoryInterface $couponFactory, RepositoryInterface $repository, EntityManagerInterface $manager)
    {
        $this->beConstructedWith($couponFactory, $repository, $manager);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Generator\CouponGenerator');
    }

    function it_should_implement_Sylius_promotion_coupon_generator_interface()
    {
        $this->shouldImplement(CouponGeneratorInterface::class);
    }

    function it_should_generate_coupons_according_to_instruction(
        FactoryInterface $couponFactory,
        $repository,
        $manager,
        FilterCollection $filters,
        PromotionInterface $promotion,
        CouponInterface $coupon,
        Instruction $instruction
    ) {
        $instruction->getAmount()->willReturn(1);
        $instruction->getUsageLimit()->willReturn(null);
        $instruction->getExpiresAt()->willReturn(null);

        $couponFactory->createNew()->willReturn($coupon);
        $repository->findOneBy(Argument::any())->willReturn(null);

        $coupon->setPromotion($promotion)->shouldBeCalled();
        $coupon->setCode(Argument::any())->shouldBeCalled();
        $coupon->setUsageLimit(null)->shouldBeCalled();
        $coupon->setExpiresAt(null)->shouldBeCalled();

        $manager->getFilters()->shouldBeCalled()->willReturn($filters);
        $filters->disable('softdeleteable')->shouldBeCalled();
        $filters->enable('softdeleteable')->shouldBeCalled();

        $manager->persist($coupon)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->generate($promotion, $instruction);
    }
}
