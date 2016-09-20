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
use Sylius\Component\Core\Model\Image;
use Sylius\Component\Core\Model\ProductImage;

/**
 * @mixin ProductImage
 *
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class ProductImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductImage::class);
    }

    function it_extends_an_image()
    {
        $this->shouldHaveType(Image::class);
    }
}
