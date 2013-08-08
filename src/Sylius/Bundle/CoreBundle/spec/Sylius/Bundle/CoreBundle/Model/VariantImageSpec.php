<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Model;

use PhpSpec\ObjectBehavior;

class VariantImageSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\VariantImage');
    }

    function it_should_be_Sylius_Image()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Model\Image');
    }

    function it_does_not_have_variant_by_default()
    {
        $this->getVariant()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\VariantInterface $variant
     */
    function its_variant_is_mutable($variant)
    {
        $this->setVariant($variant);
        $this->getVariant()->shouldReturn($variant);
    }
}
