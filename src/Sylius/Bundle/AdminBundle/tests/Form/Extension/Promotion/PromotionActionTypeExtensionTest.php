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

namespace Tests\Sylius\Bundle\AdminBundle\Form\Extension\Promotion;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Form\Builder\ChannelExclusionFormBuilder;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\PromotionActionTypeExtension;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;

#[CoversClass(PromotionActionTypeExtension::class)]
final class PromotionActionTypeExtensionTest extends TestCase
{
    public function testItExtendsPromotionActionType(): void
    {
        $this->assertSame([PromotionActionType::class], iterator_to_array(PromotionActionTypeExtension::getExtendedTypes()));
    }

    public function testItDelegatesFormBuildingToChannelExclusionFormBuilder(): void
    {
        $channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $channelRepository->method('findAll')->willReturn([]);

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnSelf();
        $builder->expects($this->atLeastOnce())->method('addEventListener')->willReturnSelf();

        (new PromotionActionTypeExtension(new ChannelExclusionFormBuilder($channelRepository)))
            ->buildForm($builder, []);
    }

    public function testItDoesNothingWhenNoChannelExclusionFormBuilderIsInjected(): void
    {
        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->expects($this->never())->method('add');
        $builder->expects($this->never())->method('addEventListener');

        (new PromotionActionTypeExtension())->buildForm($builder, []);
    }
}
