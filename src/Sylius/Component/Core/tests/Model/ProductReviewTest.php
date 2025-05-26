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

namespace Tests\Sylius\Component\Core\Model;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Model\Review;

final class ProductReviewTest extends TestCase
{
    private ProductReview $productReview;

    protected function setUp(): void
    {
        $this->productReview = new ProductReview();
    }

    public function testShouldExtendReview(): void
    {
        $this->assertInstanceOf(Review::class, $this->productReview);
    }
}
