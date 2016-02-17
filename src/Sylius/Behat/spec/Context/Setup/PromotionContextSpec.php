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
use Sylius\Component\Promotion\Model\ActionInterface;
use Sylius\Component\Promotion\Repository\PromotionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        PromotionRepositoryInterface $promotionRepository,
        RepositoryInterface $actionRepository,
        TestPromotionFactoryInterface $testPromotionFactory,
        ObjectManager $objectManager
    ) {
        $this->beConstructedWith(
            $sharedStorage,
            $promotionRepository,
            $actionRepository,
            $testPromotionFactory,
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
        $promotionRepository,
        $testPromotionFactory,
        ChannelInterface $channel,
        PromotionInterface $promotion
    ) {
        $testPromotionFactory->createPromotion('Super promotion')->willReturn($promotion);

        $sharedStorage->getCurrentResource('channel')->willReturn($channel);
        $promotion->addChannel($channel)->shouldBeCalled();

        $promotionRepository->add($promotion)->shouldBeCalled();
        $sharedStorage->setCurrentResource('promotion', $promotion)->shouldBeCalled();

        $this->thereIsPromotion('Super promotion');
    }

    function it_creates_fixed_discount_action_for_promotion(
        $actionRepository,
        $objectManager,
        $sharedStorage,
        $testPromotionFactory,
        ActionInterface $action,
        PromotionInterface $promotion
    ) {
        $sharedStorage->getCurrentResource('promotion')->willReturn($promotion);

        $testPromotionFactory->createFixedDiscountAction('10.00', $promotion)->willReturn($action);
        $actionRepository->add($action)->shouldBeCalled();

        $objectManager->flush()->shouldBeCalled();

        $this->itGivesFixedDiscountForCustomersWithCartsAbove('10.00');
    }
}
