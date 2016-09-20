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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\TaxonImage;
use Sylius\Component\Core\Model\TaxonInterface;

/**
 * @mixin TaxonImage
 *
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class TaxonImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TaxonImage::class);
    }

    function it_extends_an_image()
    {
        $this->shouldHaveType(Image::class);
    }
}
