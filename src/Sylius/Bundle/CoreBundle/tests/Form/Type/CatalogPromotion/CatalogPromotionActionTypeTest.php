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

namespace Tests\Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotion;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionAction\ChannelBasedFixedDiscountActionConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionActionType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionAction;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

final class CatalogPromotionActionTypeTest extends TypeTestCase
{
    private ChannelInterface&MockObject $channel;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    #[Test]
    public function it_updates_amount_of_fixed_discount(): void
    {
        $fixedDiscount = $this->setupFixedDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $fixedDiscount);
        $form->submit([
            'type' => 'fixed_discount',
            'configuration' => [
                'WEB_US' => [
                    'amount' => 20,
                ],
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('fixed_discount', $catalogPromotionAction->getType());
        $this->assertSame(['WEB_US' => ['amount' => 2000]], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_updates_amount_of_percentage_discount(): void
    {
        $percentageDiscount = $this->setupPercentageDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $percentageDiscount);
        $form->submit([
            'type' => 'percentage_discount',
            'configuration' => [
                'amount' => 10,
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('percentage_discount', $catalogPromotionAction->getType());
        $this->assertSame(['amount' => 0.1], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_changes_action_to_fixed_discount(): void
    {
        $percentageDiscount = $this->setupPercentageDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $percentageDiscount);
        $form->submit([
            'type' => 'fixed_discount',
            'configuration' => [
                'WEB_US' => [
                    'amount' => 10,
                ],
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('fixed_discount', $catalogPromotionAction->getType());
        $this->assertSame(['WEB_US' => ['amount' => 1000]], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_changes_action_to_percentage_discount(): void
    {
        $fixedDiscount = $this->setupFixedDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $fixedDiscount);
        $form->submit([
            'type' => 'percentage_discount',
            'configuration' => [
                'amount' => 10,
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('percentage_discount', $catalogPromotionAction->getType());
        $this->assertSame(['amount' => 0.1], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_changes_action_to_percentage_discount_with_empty_amount(): void
    {
        $fixedDiscount = $this->setupFixedDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $fixedDiscount);
        $form->submit([
            'type' => 'percentage_discount',
            'configuration' => [
                'amount' => '',
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('percentage_discount', $catalogPromotionAction->getType());
        $this->assertSame(['amount' => null], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_changes_action_to_fixed_discount_with_empty_amount(): void
    {
        $percentageDiscount = $this->setupPercentageDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $percentageDiscount);
        $form->submit([
            'type' => 'fixed_discount',
            'configuration' => [
                'WEB_US' => [
                    'amount' => '',
                ],
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('fixed_discount', $catalogPromotionAction->getType());
        $this->assertSame(['WEB_US' => ['amount' => null]], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_updates_fixed_discount_with_not_valid_amount(): void
    {
        $fixedDiscount = $this->setupFixedDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $fixedDiscount);
        $form->submit([
            'type' => 'fixed_discount',
            'configuration' => [
                'WEB_US' => [
                    'amount' => 'Not valid amount',
                ],
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('fixed_discount', $catalogPromotionAction->getType());
        $this->assertSame(['WEB_US' => ['amount' => 10]], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_updates_fixed_discount_with_float_amount(): void
    {
        $fixedDiscount = $this->setupFixedDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $fixedDiscount);
        $form->submit([
            'type' => 'fixed_discount',
            'configuration' => [
                'WEB_US' => [
                    'amount' => 20.54,
                ],
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('fixed_discount', $catalogPromotionAction->getType());
        $this->assertSame(['WEB_US' => ['amount' => 2054]], $catalogPromotionAction->getConfiguration());
    }

    #[Test]
    public function it_updates_percentage_discount_with_not_valid_amount(): void
    {
        $percentageDiscount = $this->setupPercentageDiscount();

        $this->channelRepository->method('findAll')->willReturn(
            [$this->channel],
        );

        $form = $this->factory->create(CatalogPromotionActionType::class, $percentageDiscount);
        $form->submit([
            'type' => 'percentage_discount',
            'configuration' => [
                'amount' => 'Not valid amount',
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $catalogPromotionAction = $form->getData();

        $this->assertInstanceOf(CatalogPromotionActionInterface::class, $catalogPromotionAction);
        $this->assertSame('percentage_discount', $catalogPromotionAction->getType());
        $this->assertSame(['amount' => 0.1], $catalogPromotionAction->getConfiguration());
    }

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);

        $currency = $this->createMock(CurrencyInterface::class);
        $currency->method('getCode')->willReturn('USD');

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('WEB_US');
        $channel->method('getName')->willReturn('United States');
        $channel->method('getBaseCurrency')->willReturn($currency);
        $this->channel = $channel;

        parent::setUp();
    }

    protected function getExtensions(): array
    {
        $catalogPromotionActionType = new CatalogPromotionActionType(
            CatalogPromotionActionInterface::class,
            ['sylius'],
            [
                'percentage_discount' => new PercentageDiscountActionConfigurationType(),
                'fixed_discount' => new ChannelBasedFixedDiscountActionConfigurationType(),
            ],
        );

        $channelCollectionType = new ChannelCollectionType(
            $this->channelRepository,
        );

        $validator = Validation::createValidatorBuilder()->getValidator();

        return [
            new PreloadedExtension([$catalogPromotionActionType, $channelCollectionType], []),
            new ValidatorExtension($validator),
        ];
    }

    private function setupPercentageDiscount(): CatalogPromotionActionInterface
    {
        $percentageDiscount = new CatalogPromotionAction();
        $percentageDiscount->setType('percentage_discount');
        $percentageDiscount->setCatalogPromotion(null);
        $percentageDiscount->setConfiguration(['amount' => 0.1]);

        return $percentageDiscount;
    }

    private function setupFixedDiscount(): CatalogPromotionActionInterface
    {
        $fixedDiscount = new CatalogPromotionAction();
        $fixedDiscount->setType('fixed_discount');
        $fixedDiscount->setCatalogPromotion(null);
        $fixedDiscount->setConfiguration(['WEB_US' => ['amount' => 10]]);

        return $fixedDiscount;
    }
}
