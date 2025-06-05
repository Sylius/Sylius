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

namespace Tests\Sylius\Bundle\AdminBundle\PendingAction\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountProductVariantsOutOfStockProvider;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;

final class CountProductVariantsOutOfStockProviderTest extends TestCase
{
    private MockObject&ProductVariantRepositoryInterface $productVariantRepository;

    private CountProductVariantsOutOfStockProvider $countProductVariantsOutOfStockProvider;

    protected function setUp(): void
    {
        $this->productVariantRepository = $this->createMock(ProductVariantRepositoryInterface::class);
        $this->countProductVariantsOutOfStockProvider = new CountProductVariantsOutOfStockProvider($this->productVariantRepository);
    }

    public function testCountOutOfStockProductVariants(): void
    {
        $this->productVariantRepository
            ->expects($this->once())
            ->method('countTrackedOutOfStock')
            ->willReturn(5)
        ;

        $this->assertSame(5, $this->countProductVariantsOutOfStockProvider->count());
    }
}
