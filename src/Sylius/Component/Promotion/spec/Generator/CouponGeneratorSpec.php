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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Promotion\Generator\Instruction;
use Sylius\Component\Promotion\Model\CouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Manager\ResourceManagerInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CouponGeneratorSpec extends ObjectBehavior
{
    function let(ResourceFactoryInterface $factory, ResourceRepositoryInterface $repository, ResourceManagerInterface $manager)
    {
        $this->beConstructedWith($factory, $repository, $manager);
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
        $factory,
        $repository,
        $manager,
        PromotionInterface $promotion,
        CouponInterface $coupon,
        Instruction $instruction
    ) {
        $instruction->getAmount()->willReturn(1);
        $instruction->getUsageLimit()->willReturn(null);
        $instruction->getExpiresAt()->willReturn(null);

        $factory->createNew()->willReturn($coupon);
        $repository->findOneBy(Argument::any())->willReturn(null);

        $coupon->setPromotion($promotion)->shouldBeCalled();
        $coupon->setCode(Argument::any())->shouldBeCalled();
        $coupon->setUsageLimit(null)->shouldBeCalled();
        $coupon->setExpiresAt(null)->shouldBeCalled();

        $repository->disableFilter('softdeleteable')->shouldBeCalled();
        $repository->enableFilter('softdeleteable')->shouldBeCalled();

        $manager->persist($coupon)->shouldBeCalled();
        $manager->flush()->shouldBeCalled();

        $this->generate($promotion, $instruction);
    }
}
