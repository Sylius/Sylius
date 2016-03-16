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
use Sylius\Component\Core\Factory\RuleFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Promotion\Factory\ActionFactoryInterface;
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        ActionFactoryInterface $actionFactory,
        RuleFactoryInterface $ruleFactory,
        TestPromotionFactoryInterface $testPromotionFactory,
        PromotionRepositoryInterface $promotionRepository,
        ObjectManager $objectManager
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $actionFactory,
            $ruleFactory,
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
        $sharedStorage->get('channel')->willReturn($channel);

        $testPromotionFactory->createForChannel('Super promotion', $channel)->willReturn($promotion);

        $promotionRepository->add($promotion)->shouldBeCalled();
        $sharedStorage->set('promotion', $promotion)->shouldBeCalled();

        $this->thereIsPromotion('Super promotion');
    }

    function it_creates_fixed_discount_action_for_promotion(
        $actionFactory,
        $objectManager,
        ActionInterface $action,
        PromotionInterface $promotion
    ) {
        $actionFactory->createFixedDiscount(1000)->willReturn($action);
        $promotion->addAction($action)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesFixedDiscountToEveryOrder($promotion, 1000);
    }

    function it_creates_percentage_discount_action_for_promotion(
        $sharedStorage,
        $actionFactory,
        $objectManager,
        ActionInterface $action,
        PromotionInterface $promotion
    ) {
        $sharedStorage->get('promotion')->willReturn($promotion);

        $actionFactory->createPercentageDiscount(0.1)->willReturn($action);
        $promotion->addAction($action)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesPercentageDiscountToEveryOrder($promotion, 0.1);
    }

    function it_creates_fixed_discount_promotion_for_cart_with_specified_quantity(
        $sharedStorage,
        $actionFactory,
        $ruleFactory,
        $objectManager,
        ActionInterface $action,
        RuleInterface $rule,
        PromotionInterface $promotion
    ) {
        $sharedStorage->get('promotion')->willReturn($promotion);

        $actionFactory->createFixedDiscount(1000)->willReturn($action);
        $promotion->addAction($action)->shouldBeCalled();

        $ruleFactory->createCartQuantity(5)->willReturn($rule);
        $promotion->addRule($rule)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesFixedDiscountToEveryOrderWithQuantityAtLeast($promotion, 1000, '5');
    }

    function it_creates_fixed_discount_promotion_for_cart_with_specified_items_total(
        $actionFactory,
        $ruleFactory,
        $objectManager,
        ActionInterface $action,
        RuleInterface $rule,
        PromotionInterface $promotion
    ) {
        $actionFactory->createFixedDiscount(1000)->willReturn($action);
        $promotion->addAction($action)->shouldBeCalled();

        $ruleFactory->createItemTotal(5000)->willReturn($rule);
        $promotion->addRule($rule)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesFixedDiscountToEveryOrderWithItemsTotalAtLeast($promotion, 1000, 5000);
    }

    function it_creates_percentage_shipping_discount_action_for_promotion(
        $actionFactory,
        $objectManager,
        ActionInterface $action,
        PromotionInterface $promotion
    ) {
        $actionFactory->createPercentageShippingDiscount(0.1)->willReturn($action);
        $promotion->addAction($action)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesPercentageDiscountOnShippingToEveryOrder($promotion, 0.1);
    }
}
