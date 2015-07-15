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
use Sylius\Component\Core\Model\ProductVariantInterface;

class ProductVariantImageSpec extends ObjectBehavior
{
    public function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\ProductVariantImage');
    }

    public function it_should_be_Sylius_Image()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Image');
    }

    public function it_does_not_have_variant_by_default()
    {
        $this->getVariant()->shouldReturn(null);
    }

    public function its_variant_is_mutable(ProductVariantInterface $variant)
    {
        $this->setVariant($variant);
        $this->getVariant()->shouldReturn($variant);
    }
}
