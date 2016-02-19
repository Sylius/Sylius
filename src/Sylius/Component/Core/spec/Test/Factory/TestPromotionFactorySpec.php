<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Test\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TestPromotionFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $promotionFactory)
    {
        $this->beConstructedWith($promotionFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Test\Factory\TestPromotionFactory');
    }

    function it_implements_test_promotion_factory_interface()
    {
        $this->shouldImplement(TestPromotionFactoryInterface::class);
    }

    function it_creates_promotion_with_given_name($promotionFactory, PromotionInterface $promotion)
    {
        $promotionFactory->createNew()->willReturn($promotion);
        $promotion->setName('Super promotion')->shouldBeCalled();
        $promotion->setCode('super_promotion')->shouldBeCalled();
        $promotion->setDescription('Promotion Super promotion')->shouldBeCalled();
        $promotion->setStartsAt(Argument::type('\DateTime'))->shouldBeCalled();
        $promotion->setEndsAt(Argument::type('\DateTime'))->shouldBeCalled();

        $this->create('Super promotion')->shouldReturn($promotion);
    }

    function it_creates_promotion_with_given_name_and_channel(
        $promotionFactory,
        ChannelInterface $channel,
        PromotionInterface $promotion
    ) {
        $promotionFactory->createNew()->willReturn($promotion);
        $promotion->setName('Super promotion')->shouldBeCalled();
        $promotion->setCode('super_promotion')->shouldBeCalled();
        $promotion->setDescription('Promotion Super promotion')->shouldBeCalled();
        $promotion->setStartsAt(Argument::type('\DateTime'))->shouldBeCalled();
        $promotion->setEndsAt(Argument::type('\DateTime'))->shouldBeCalled();
        $promotion->addChannel($channel)->shouldBeCalled();

        $this->createForChannel('Super promotion', $channel)->shouldReturn($promotion);
    }
}
