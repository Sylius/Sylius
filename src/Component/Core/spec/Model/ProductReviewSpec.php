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

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Model\Review;

final class ProductReviewSpec extends ObjectBehavior
{
    function it_extends_a_review(): void
    {
        $this->shouldHaveType(Review::class);
    }
}
