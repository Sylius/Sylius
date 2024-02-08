<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\CatalogPromotion\Validator\CatalogPromotionAction;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PromotionBundle\Validator\CatalogPromotionAction\ActionValidatorInterface;
use Sylius\Bundle\PromotionBundle\Validator\Constraints\CatalogPromotionAction;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\CatalogPromotionInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class FixedDiscountActionValidatorSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository): void
    {
        $this->beConstructedWith($channelRepository);
    }

    function it_is_an_action_validator(): void
    {
        $this->shouldHaveType(ActionValidatorInterface::class);
    }

    function it_adds_violation_if_catalog_promotion_action_has_an_empty_configuration(
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
    ): void {
        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate([], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_configured_channel_that_does_not_exist(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $channelRepository->findOneBy(['code' => 'nonexistent_channel'])->willReturn(null);

        $executionContext->getObject()->willReturn($catalogPromotionAction);
        $catalogPromotionAction->getCatalogPromotion()->willReturn($catalogPromotion);
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([]));

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.invalid_channel')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['nonexistent_channel' => ['amount' => 1000]], new CatalogPromotionAction(), $executionContext);
    }

    function it_does_nothing_if_the_provided_configuration_is_valid(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext,
        ChannelInterface $channel,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $channelRepository->findOneBy(['code' => 'channel'])->willReturn($channel);

        $executionContext->getObject()->willReturn($catalogPromotionAction);
        $catalogPromotionAction->getCatalogPromotion()->willReturn($catalogPromotion);
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channel->getWrappedObject()]));
        $channel->getCode()->willReturn('channel');

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.invalid_channel')->shouldNotBeCalled();
        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.not_valid')->shouldNotBeCalled();

        $this->validate(['channel' => ['amount' => 1000]], new CatalogPromotionAction(), $executionContext);
    }

    function it_adds_violation_if_catalog_promotion_action_has_not_specified_channel_fixed_discount(
        ChannelRepositoryInterface $channelRepository,
        ExecutionContextInterface $executionContext,
        ConstraintViolationBuilderInterface $constraintViolationBuilder,
        ChannelInterface $channelOne,
        ChannelInterface $channelTwo,
        CatalogPromotionActionInterface $catalogPromotionAction,
        CatalogPromotionInterface $catalogPromotion,
    ): void {
        $executionContext->getObject()->willReturn($catalogPromotionAction);
        $catalogPromotionAction->getCatalogPromotion()->willReturn($catalogPromotion);
        $catalogPromotion->getChannels()->willReturn(new ArrayCollection([$channelOne->getWrappedObject(), $channelTwo->getWrappedObject()]));
        $channelOne->getCode()->willReturn('channel');
        $channelTwo->getCode()->willReturn('channel_two');

        $executionContext->buildViolation('sylius.catalog_promotion_action.fixed_discount.channel_not_configured')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->atPath('configuration')->willReturn($constraintViolationBuilder);
        $constraintViolationBuilder->addViolation()->shouldBeCalled();

        $this->validate(['channel' => ['amount' => 100], 'channel_two' => ['amount' => null]], new CatalogPromotionAction(), $executionContext);
    }
}
