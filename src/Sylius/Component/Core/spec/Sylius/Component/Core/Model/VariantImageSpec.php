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
use Sylius\Component\Core\Model\VariantInterface;

class VariantImageSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\VariantImage');
    }

    function it_should_be_Sylius_Image()
    {
        $this->shouldHaveType('Sylius\Component\Core\Model\Image');
    }

    function it_does_not_have_variant_by_default()
    {
        $this->getVariant()->shouldReturn(null);
    }

    function its_variant_is_mutable(VariantInterface $variant)
    {
        $this->setVariant($variant);
        $this->getVariant()->shouldReturn($variant);
    }
}
