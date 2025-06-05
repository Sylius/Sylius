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

namespace Tests\Sylius\Bundle\ProductBundle\Form\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\BuildProductVariantFormSubscriber;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueCollectionType;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class BuildProductVariantFormSubscriberTest extends TestCase
{
    private FormFactoryInterface&MockObject $factory;

    private BuildProductVariantFormSubscriber $buildProductVariantFormSubscriber;

    protected function setUp(): void
    {
        $this->factory = $this->createMock(FormFactoryInterface::class);

        $this->buildProductVariantFormSubscriber = new BuildProductVariantFormSubscriber($this->factory);
    }

    public function testSubscribesToEvent(): void
    {
        $this->assertSame(
            [FormEvents::PRE_SET_DATA => 'preSetData'],
            BuildProductVariantFormSubscriber::getSubscribedEvents(),
        );
    }

    public function testAddsOptionsOnPreSetDataEventWithConfigurableOptions(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $optionsForm */
        $optionsForm = $this->createMock(FormInterface::class);
        /** @var ProductInterface&MockObject $variable */
        $variable = $this->createMock(ProductInterface::class);
        /** @var ProductOptionInterface&MockObject $options */
        $options = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionValueInterface&MockObject $optionValue */
        $optionValue = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $event->expects($this->once())->method('getData')->willReturn($variant);
        $variant->expects($this->once())->method('getProduct')->willReturn($variable);
        $variant->expects($this->once())->method('getOptionValues')->willReturn(new ArrayCollection([$optionValue]));
        $variable->expects($this->once())->method('getOptions')->willReturn(new ArrayCollection([$options]));
        $variable->expects($this->once())->method('hasOptions')->willReturn(true);

        $this
            ->factory->expects($this->once())
            ->method('createNamed')
            ->with('optionValues', ProductOptionValueCollectionType::class, new ArrayCollection([$optionValue]), [
                'options' => new ArrayCollection([$options]),
                'auto_initialize' => false,
                'disabled' => false,
            ])
            ->willReturn($optionsForm)
        ;
        $form->expects($this->once())->method('add')->with($optionsForm)->willReturn($form);

        $this->buildProductVariantFormSubscriber->preSetData($event);
    }

    public function testAddsOptionsOnPreSetDataEventWithoutConfigurableOptions(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var FormInterface&MockObject $form */
        $form = $this->createMock(FormInterface::class);
        /** @var FormInterface&MockObject $optionsForm */
        $optionsForm = $this->createMock(FormInterface::class);
        /** @var ProductInterface&MockObject $variable */
        $variable = $this->createMock(ProductInterface::class);
        /** @var ProductOptionInterface&MockObject $options */
        $options = $this->createMock(ProductOptionInterface::class);
        /** @var ProductOptionValueInterface&MockObject $optionValue */
        $optionValue = $this->createMock(ProductOptionValueInterface::class);
        /** @var ProductVariantInterface&MockObject $variant */
        $variant = $this->createMock(ProductVariantInterface::class);

        $this->buildProductVariantFormSubscriber = new BuildProductVariantFormSubscriber($this->factory, true);

        $event->expects($this->once())->method('getForm')->willReturn($form);
        $event->expects($this->once())->method('getData')->willReturn($variant);
        $variant->expects($this->once())->method('getProduct')->willReturn($variable);
        $variant->expects($this->once())->method('getOptionValues')->willReturn(new ArrayCollection([$optionValue]));
        $variable->expects($this->once())->method('getOptions')->willReturn(new ArrayCollection([$options]));
        $variable->expects($this->once())->method('hasOptions')->willReturn(true);

        $this->factory
            ->expects($this->once())
            ->method('createNamed')->with('optionValues', ProductOptionValueCollectionType::class, new ArrayCollection([$optionValue]), [
                'options' => new ArrayCollection([$options]),
                'auto_initialize' => false,
                'disabled' => true,
            ])
            ->willReturn($optionsForm)
        ;
        $form->expects($this->once())->method('add')->with($optionsForm)->willReturn($form);

        $this->buildProductVariantFormSubscriber->preSetData($event);
    }
}
