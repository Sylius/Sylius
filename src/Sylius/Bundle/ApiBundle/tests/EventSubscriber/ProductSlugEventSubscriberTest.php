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

namespace Tests\Sylius\Bundle\ApiBundle\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\EventSubscriber\ProductSlugEventSubscriber;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ProductSlugEventSubscriberTest extends TestCase
{
    private MockObject&SlugGeneratorInterface $slugGenerator;

    private ProductSlugEventSubscriber $productSlugEventSubscriber;

    protected function setUp(): void
    {
        parent::setUp();
        $this->slugGenerator = $this->createMock(SlugGeneratorInterface::class);
        $this->productSlugEventSubscriber = new ProductSlugEventSubscriber($this->slugGenerator);
    }

    public function testGeneratesSlugForProductWithNameAndEmptySlug(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductTranslationInterface|MockObject $productTranslationMock */
        $productTranslationMock = $this->createMock(ProductTranslationInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $productMock->expects(self::once())
            ->method('getTranslations')
            ->willReturn(new ArrayCollection([$productTranslationMock]));

        $productTranslationMock->expects(self::once())->method('getSlug')->willReturn(null);

        $productTranslationMock->expects(self::atLeastOnce())->method('getName')->willReturn('Audi RS7');

        $this->slugGenerator->expects(self::once())
            ->method('generate')
            ->with('Audi RS7')
            ->willReturn('audi-rs7');

        $productTranslationMock->expects(self::once())->method('setSlug')->with('audi-rs7');

        $this->productSlugEventSubscriber->generateSlug(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $productMock,
        ));
    }

    public function testDoesNothingIfTheProductHasSlug(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductTranslationInterface|MockObject $productTranslationMock */
        $productTranslationMock = $this->createMock(ProductTranslationInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $productMock->expects(self::once())
            ->method('getTranslations')
            ->willReturn(new ArrayCollection([$productTranslationMock]));

        $productTranslationMock->expects(self::atLeastOnce())->method('getSlug')->willReturn('audi-rs7');

        $productTranslationMock->expects(self::never())->method('getName');

        $this->slugGenerator->expects(self::never())->method('generate');

        $productTranslationMock->expects(self::never())->method('setSlug');

        $this->productSlugEventSubscriber->generateSlug(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $productMock,
        ));
    }

    public function testDoesNothingIfTheProductHasNoName(): void
    {
        /** @var ProductInterface|MockObject $productMock */
        $productMock = $this->createMock(ProductInterface::class);
        /** @var ProductTranslationInterface|MockObject $productTranslationMock */
        $productTranslationMock = $this->createMock(ProductTranslationInterface::class);
        /** @var HttpKernelInterface|MockObject $kernelMock */
        $kernelMock = $this->createMock(HttpKernelInterface::class);
        /** @var Request|MockObject $requestMock */
        $requestMock = $this->createMock(Request::class);

        $requestMock->expects(self::once())->method('getMethod')->willReturn(Request::METHOD_POST);

        $productMock->expects(self::once())
            ->method('getTranslations')
            ->willReturn(new ArrayCollection([$productTranslationMock]));

        $productTranslationMock->expects(self::once())->method('getSlug')->willReturn(null);

        $productTranslationMock->expects(self::once())->method('getName')->willReturn(null);

        $this->slugGenerator->expects(self::never())->method('generate');

        $productTranslationMock->expects(self::never())->method('setSlug');

        $this->productSlugEventSubscriber->generateSlug(new ViewEvent(
            $kernelMock,
            $requestMock,
            HttpKernelInterface::MAIN_REQUEST,
            $productMock,
        ));
    }
}
