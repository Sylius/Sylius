<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Form\ChoiceList;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\VariantInterface;

final class VariantChoiceListSpec extends ObjectBehavior
{
    function let(ProductInterface $variable, VariantInterface $variant)
    {
        $variable->getVariants()->shouldBeCalled()->willReturn([$variant]);

        $this->beConstructedWith($variable);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Form\ChoiceList\VariantChoiceList');
    }
}
