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
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\BuildAttributesFormSubscriber;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Resource\Factory\FactoryInterface;
use Sylius\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class BuildAttributesFormSubscriberTest extends TestCase
{
    private FactoryInterface&MockObject $attributeValueFactory;

    private MockObject&TranslationLocaleProviderInterface $localeProvider;

    private BuildAttributesFormSubscriber $buildAttributesFormSubscriber;

    protected function setUp(): void
    {
        $this->attributeValueFactory = $this->createMock(FactoryInterface::class);
        $this->localeProvider = $this->createMock(TranslationLocaleProviderInterface::class);

        $this->buildAttributesFormSubscriber = new BuildAttributesFormSubscriber($this->attributeValueFactory, $this->localeProvider);
    }

    public function testSubscribesToEvent(): void
    {
        $this->assertSame(
            [
                FormEvents::PRE_SET_DATA => 'preSetData',
                FormEvents::POST_SUBMIT => 'postSubmit',
            ],
            BuildAttributesFormSubscriber::getSubscribedEvents(),
        );
    }

    public function testAddsAttributeValuesInDifferentLocalesToAProduct(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductAttributeInterface&MockObject $attribute */
        $attribute = $this->createMock(ProductAttributeInterface::class);
        /** @var ProductAttributeValueInterface&MockObject $attributeValue */
        $attributeValue = $this->createMock(ProductAttributeValueInterface::class);
        /** @var ProductAttributeValueInterface&MockObject $newAttributeValue */
        $newAttributeValue = $this->createMock(ProductAttributeValueInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($product);

        $this->localeProvider->expects($this->once())->method('getDefinedLocalesCodes')->willReturn(['en_US', 'pl_PL']);
        $this->localeProvider->expects($this->once())->method('getDefaultLocaleCode')->willReturn('en_US');

        $attributeValue->expects($this->once())->method('getAttribute')->willReturn($attribute);
        $attributeValue->expects($this->once())->method('getLocaleCode')->willReturn('en_US');
        $attributeValue->expects($this->exactly(2))->method('getCode')->willReturn('mug_material');

        $product->expects($this->once())->method('getAttributes')->willReturn(new ArrayCollection([$attributeValue]));
        $product
            ->expects($this->exactly(2))
            ->method('hasAttributeByCodeAndLocale')
            ->willReturnMap([['mug_material', 'en_US', true], ['mug_material', 'pl_PL', false]])
        ;

        $this->attributeValueFactory->expects($this->once())->method('createNew')->willReturn($newAttributeValue);

        $newAttributeValue->expects($this->once())->method('setAttribute')->with($attribute);
        $newAttributeValue->expects($this->once())->method('setLocaleCode')->with('pl_PL');

        $product->expects($this->once())->method('addAttribute')->with($newAttributeValue);

        $this->buildAttributesFormSubscriber->preSetData($event);
    }

    public function testRemovesEmptyAttributeValuesInDifferentLocales(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var ProductAttributeValueInterface&MockObject $attributeValue */
        $attributeValue = $this->createMock(ProductAttributeValueInterface::class);
        /** @var ProductAttributeValueInterface&MockObject $attributeValue2 */
        $attributeValue2 = $this->createMock(ProductAttributeValueInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($product);
        $product
            ->expects($this->once())
            ->method('getAttributes')
            ->willReturn(new ArrayCollection([$attributeValue, $attributeValue2]))
        ;
        $product->expects($this->once())->method('removeAttribute')->with($attributeValue);

        $attributeValue->expects($this->once())->method('getValue')->willReturn(null);
        $attributeValue2->expects($this->once())->method('getValue')->willReturn('yellow');

        $this->buildAttributesFormSubscriber->postSubmit($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfDataIsNotAProduct(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->buildAttributesFormSubscriber->preSetData($event);
    }

    public function testThrowsAnInvalidArgumentExceptionIfDataIsNotAProductDuringSubmit(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);

        $event->expects($this->once())->method('getData')->willReturn(new \stdClass());

        $this->expectException(\InvalidArgumentException::class);

        $this->buildAttributesFormSubscriber->postSubmit($event);
    }
}
