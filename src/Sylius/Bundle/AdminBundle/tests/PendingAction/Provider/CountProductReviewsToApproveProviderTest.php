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
use Sylius\Bundle\AdminBundle\PendingAction\Provider\CountProductReviewsToApproveProvider;
use Sylius\Component\Core\Repository\ProductReviewRepositoryInterface;

final class CountProductReviewsToApproveProviderTest extends TestCase
{
    private MockObject&ProductReviewRepositoryInterface $productReviewRepository;

    private CountProductReviewsToApproveProvider $countProductReviewsToApproveProvider;

    protected function setUp(): void
    {
        $this->productReviewRepository = $this->createMock(ProductReviewRepositoryInterface::class);
        $this->countProductReviewsToApproveProvider = new CountProductReviewsToApproveProvider($this->productReviewRepository);
    }

    public function testCountProductReviewsToAccept(): void
    {
        $this->productReviewRepository->expects($this->once())->method('countNew')->willReturn(5);

        $this->assertSame(5, $this->countProductReviewsToApproveProvider->count());
    }
}
