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

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Generator\Instruction;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CouponGeneratorSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $repository,
        ObjectManager $manager
    )
    {
        $this->beConstructedWith($repository, $manager);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Promotion\Generator\CouponGenerator');
    }

    function it_should_implement_Sylius_promotion_coupon_generator_interface()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Generator\CouponGeneratorInterface');
    }

    function it_should_generate_coupons_according_to_instruction(
        $repository,
        $manager,
        PromotionInterface $promotion,
        CouponInterface $coupon,
        Instruction $instruction
    )
    {
        $instruction->getAmount()->willReturn(1);
        $instruction->getUsageLimit()->willReturn(null);

        $repository->createNew()->willReturn($coupon);
        $repository->findOneBy(Argument::any())->willReturn(null);

        $coupon->setPromotion($promotion)->shouldBeCalled();
        $coupon->setCode(Argument::any())->shouldBeCalled();
        $coupon->setUsageLimit(null)->shouldBeCalled();

        $manager->persist($coupon)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->generate($promotion, $instruction);
    }
}
