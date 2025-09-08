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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\ProductOptionValueDeletionListener;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;

final class ProductOptionValueDeletionListenerTest extends TestCase
{
    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private ProductOptionValueDeletionListener $productOptionValueDeletionListener;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->productOptionValueDeletionListener = new ProductOptionValueDeletionListener($this->productVariantRepository);
    }

    public function testThrowsResourceDeleteExceptionIfProductVariantsExistForOptionValue(): void
    {
        $optionValue = $this->createMock(ProductOptionValueInterface::class);

        $optionValue->expects($this->once())->method('getId')->willReturn(1);

        $this->productVariantRepository
            ->expects($this->once())
            ->method('countByProductOptionValueId')
            ->with(1)
            ->willReturn(1)
        ;

        $this->expectException(ResourceDeleteException::class);

        $this->productOptionValueDeletionListener->preRemove($optionValue);
    }

    public function testDoesNothingIfNoProductVariantsExistForOptionValue(): void
    {
        $optionValue = $this->createMock(ProductOptionValueInterface::class);

        $optionValue->expects($this->once())
            ->method('getId')
            ->willReturn(1)
        ;

        $this->productVariantRepository
            ->expects($this->once())
            ->method('countByProductOptionValueId')
            ->with(1)
            ->willReturn(0)
        ;

        $this->productOptionValueDeletionListener->preRemove($optionValue);
    }
}
