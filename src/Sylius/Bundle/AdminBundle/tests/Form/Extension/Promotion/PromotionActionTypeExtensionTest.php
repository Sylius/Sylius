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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\PromotionActionTypeExtension;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

#[CoversClass(PromotionActionTypeExtension::class)]
final class PromotionActionTypeExtensionTest extends TestCase
{
    private const KEY = ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private PromotionActionTypeExtension $extension;

    /** @var array<string, callable> */
    private array $listeners = [];

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);

        $webChannel = $this->createMock(ChannelInterface::class);
        $webChannel->method('getName')->willReturn('Web');
        $webChannel->method('getCode')->willReturn('WEB');

        $mobileChannel = $this->createMock(ChannelInterface::class);
        $mobileChannel->method('getName')->willReturn('Mobile');
        $mobileChannel->method('getCode')->willReturn('MOBILE');

        $this->channelRepository->method('findAll')->willReturn([$webChannel, $mobileChannel]);

        $this->extension = new PromotionActionTypeExtension($this->channelRepository);

        $builder = $this->createMock(FormBuilderInterface::class);
        $builder->method('add')->willReturnSelf();
        $builder->method('addEventListener')->willReturnCallback(
            function (string $event, callable $listener): FormBuilderInterface {
                $this->listeners[$event] = $listener;

                return $this->createMock(FormBuilderInterface::class);
            },
        );

        $this->extension->buildForm($builder, []);
    }

    public function testPreSubmitInjectsDefaultChannelsForNewAction(): void
    {
        $innerForm = $this->createMock(FormInterface::class);
        $innerForm->method('getData')->willReturn(null);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn(['type' => 'order_percentage_discount']);
        $event->method('getForm')->willReturn($innerForm);
        $event->expects($this->once())->method('setData')->with([
            'type' => 'order_percentage_discount',
            self::KEY => ['WEB', 'MOBILE'],
        ]);

        ($this->listeners[FormEvents::PRE_SUBMIT])($event);
    }

    public function testPreSubmitDoesNotInjectForExistingActionWithNoKeyPresent(): void
    {
        $action = $this->createMock(PromotionActionInterface::class);
        $innerForm = $this->createMock(FormInterface::class);
        $innerForm->method('getData')->willReturn($action);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn(['type' => 'order_percentage_discount']);
        $event->method('getForm')->willReturn($innerForm);
        $event->expects($this->never())->method('setData');

        ($this->listeners[FormEvents::PRE_SUBMIT])($event);
    }

    public function testPreSubmitDoesNotInjectWhenKeyAlreadyPresent(): void
    {
        $innerForm = $this->createMock(FormInterface::class);
        $innerForm->method('getData')->willReturn(null);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn(['type' => 'order_percentage_discount', self::KEY => ['WEB']]);
        $event->method('getForm')->willReturn($innerForm);
        $event->expects($this->never())->method('setData');

        ($this->listeners[FormEvents::PRE_SUBMIT])($event);
    }

    public function testPostSetDataSetsAllChannelsCheckedForNewAction(): void
    {
        $channelField = $this->createMock(FormInterface::class);
        $channelField->expects($this->once())->method('setData')->with(['WEB', 'MOBILE']);

        $form = $this->createMock(FormInterface::class);
        $form->method('get')->with(self::KEY)->willReturn($channelField);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn(null);
        $event->method('getForm')->willReturn($form);

        ($this->listeners[FormEvents::POST_SET_DATA])($event);
    }

    public function testPostSetDataSetsAllChannelsCheckedWhenNoExclusions(): void
    {
        $action = $this->createMock(PromotionActionInterface::class);
        $action->method('getConfiguration')->willReturn([self::KEY => []]);

        $channelField = $this->createMock(FormInterface::class);
        $channelField->expects($this->once())->method('setData')->with(['WEB', 'MOBILE']);

        $form = $this->createMock(FormInterface::class);
        $form->method('get')->with(self::KEY)->willReturn($channelField);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn($action);
        $event->method('getForm')->willReturn($form);

        ($this->listeners[FormEvents::POST_SET_DATA])($event);
    }

    public function testPostSetDataSetsOnlyNonExcludedChannelsChecked(): void
    {
        $action = $this->createMock(PromotionActionInterface::class);
        $action->method('getConfiguration')->willReturn([self::KEY => ['MOBILE']]);

        $channelField = $this->createMock(FormInterface::class);
        $channelField->expects($this->once())->method('setData')->with(['WEB']);

        $form = $this->createMock(FormInterface::class);
        $form->method('get')->with(self::KEY)->willReturn($channelField);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn($action);
        $event->method('getForm')->willReturn($form);

        ($this->listeners[FormEvents::POST_SET_DATA])($event);
    }

    public function testSubmitComputesExcludedChannelsFromSelectedChannels(): void
    {
        $action = $this->createMock(PromotionActionInterface::class);
        $action->method('getConfiguration')->willReturn([]);
        $action->expects($this->once())->method('setConfiguration')->with([
            self::KEY => ['MOBILE'],
        ]);

        $channelField = $this->createMock(FormInterface::class);
        $channelField->method('getData')->willReturn(['WEB']);

        $form = $this->createMock(FormInterface::class);
        $form->method('get')->with(self::KEY)->willReturn($channelField);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn($action);
        $event->method('getForm')->willReturn($form);

        ($this->listeners[FormEvents::SUBMIT])($event);
    }

    public function testSubmitStoresEmptyExclusionsWhenAllChannelsSelected(): void
    {
        $action = $this->createMock(PromotionActionInterface::class);
        $action->method('getConfiguration')->willReturn([]);
        $action->expects($this->once())->method('setConfiguration')->with([
            self::KEY => [],
        ]);

        $channelField = $this->createMock(FormInterface::class);
        $channelField->method('getData')->willReturn(['WEB', 'MOBILE']);

        $form = $this->createMock(FormInterface::class);
        $form->method('get')->with(self::KEY)->willReturn($channelField);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn($action);
        $event->method('getForm')->willReturn($form);

        ($this->listeners[FormEvents::SUBMIT])($event);
    }

    public function testSubmitStoresAllChannelsExcludedWhenNoneSelected(): void
    {
        $action = $this->createMock(PromotionActionInterface::class);
        $action->method('getConfiguration')->willReturn([]);
        $action->expects($this->once())->method('setConfiguration')->with([
            self::KEY => ['WEB', 'MOBILE'],
        ]);

        $channelField = $this->createMock(FormInterface::class);
        $channelField->method('getData')->willReturn([]);

        $form = $this->createMock(FormInterface::class);
        $form->method('get')->with(self::KEY)->willReturn($channelField);

        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn($action);
        $event->method('getForm')->willReturn($form);

        ($this->listeners[FormEvents::SUBMIT])($event);
    }

    public function testSubmitDoesNothingForNonActionData(): void
    {
        $event = $this->createMock(FormEvent::class);
        $event->method('getData')->willReturn(null);
        $event->expects($this->never())->method('getForm');

        ($this->listeners[FormEvents::SUBMIT])($event);
    }
}
