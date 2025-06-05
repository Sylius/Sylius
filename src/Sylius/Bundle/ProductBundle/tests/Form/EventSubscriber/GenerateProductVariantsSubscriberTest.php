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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\GenerateProductVariantsSubscriber;
use Sylius\Component\Product\Exception\ProductWithoutOptionsValuesException;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;

final class GenerateProductVariantsSubscriberTest extends TestCase
{
    private MockObject&ProductVariantGeneratorInterface $generator;

    private MockObject&RequestStack $requestStack;

    private GenerateProductVariantsSubscriber $generateProductVariantsSubscriber;

    protected function setUp(): void
    {
        $this->generator = $this->createMock(ProductVariantGeneratorInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->generateProductVariantsSubscriber = new GenerateProductVariantsSubscriber($this->generator, $this->requestStack);
    }

    public function testSubscriber(): void
    {
        $this->assertInstanceOf(EventSubscriberInterface::class, $this->generateProductVariantsSubscriber);
    }

    public function testSubscribesToEvents(): void
    {
        $this->assertSame(
            [FormEvents::PRE_SET_DATA => 'preSetData'],
            GenerateProductVariantsSubscriber::getSubscribedEvents(),
        );
    }

    public function testGeneratesVariantsFromProduct(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($product);
        $this->generator->expects($this->once())->method('generate')->with($product);

        $this->generateProductVariantsSubscriber->preSetData($event);
    }

    public function testAddsMessageToFlashBagOnError(): void
    {
        /** @var FormEvent&MockObject $event */
        $event = $this->createMock(FormEvent::class);
        /** @var ProductInterface&MockObject $product */
        $product = $this->createMock(ProductInterface::class);
        /** @var Session&MockObject $session */
        $session = $this->createMock(Session::class);
        /** @var FlashBagInterface&MockObject $flashBag */
        $flashBag = $this->createMock(FlashBagInterface::class);

        $event->expects($this->once())->method('getData')->willReturn($product);
        $this->generator->expects($this->once())->method('generate')->with($product)->willThrowException(new ProductWithoutOptionsValuesException());
        $this->requestStack->expects($this->once())->method('getSession')->willReturn($session);
        $session->expects($this->once())->method('getBag')->with('flashes')->willReturn($flashBag);
        $flashBag->expects($this->once())->method('add')->with('error', 'sylius.product_variant.cannot_generate_variants');

        $this->generateProductVariantsSubscriber->preSetData($event);
    }
}
