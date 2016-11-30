<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\ProductReview;
use Sylius\Component\Review\Model\Review;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ProductReviewSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductReview::class);
    }

    function it_extends_a_review()
    {
        $this->shouldHaveType(Review::class);
    }
}
