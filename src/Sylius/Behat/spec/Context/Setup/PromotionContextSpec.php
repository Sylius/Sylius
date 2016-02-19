<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Factory\ActionFactoryInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        ActionFactoryInterface $actionFactory,
        TestPromotionFactoryInterface $testPromotionFactory,
        PromotionRepositoryInterface $promotionRepository,
        ObjectManager $objectManager
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $actionFactory,
            $testPromotionFactory,
            $promotionRepository,
            $objectManager
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\PromotionContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_promotion(
        $sharedStorage,
        $testPromotionFactory,
        $promotionRepository,
        ChannelInterface $channel,
        PromotionInterface $promotion
    ) {
        $testPromotionFactory->create('Super promotion')->willReturn($promotion);

        $sharedStorage->get('channel')->willReturn($channel);
        $promotion->addChannel($channel)->shouldBeCalled();

        $promotionRepository->add($promotion)->shouldBeCalled();
        $sharedStorage->set('promotion', $promotion)->shouldBeCalled();

        $this->thereIsPromotion('Super promotion');
    }

    function it_creates_fixed_discount_action_for_promotion(
        $sharedStorage,
        $actionFactory,
        $objectManager,
        ActionInterface $action,
        PromotionInterface $promotion
    ) {
        $sharedStorage->get('promotion')->willReturn($promotion);

        $actionFactory->createFixedDiscount(1000)->willReturn($action);
        $promotion->addAction($action)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesFixedDiscountToEveryOrder('10.00');
    }
}
