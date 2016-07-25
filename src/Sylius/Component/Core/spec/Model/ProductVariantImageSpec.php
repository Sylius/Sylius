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

final class ProductVariantImageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\ProductVariantImage');
    }

    function it_should_be_Sylius_Image()
    {
        $this->shouldHaveType(Image::class);
    }

    function it_does_not_have_variant_by_default()
    {
        $this->getVariant()->shouldReturn(null);
    }

    function its_variant_is_mutable(ProductVariantInterface $variant)
    {
        $this->setVariant($variant);
        $this->getVariant()->shouldReturn($variant);
    }
}
