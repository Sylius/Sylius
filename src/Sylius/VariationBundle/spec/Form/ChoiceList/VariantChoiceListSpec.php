<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\VariationBundle\Form\ChoiceList;

use PhpSpec\ObjectBehavior;
use Sylius\Variation\Model\VariableInterface;
use Sylius\Variation\Model\VariantInterface;

class VariantChoiceListSpec extends ObjectBehavior
{
    function let(VariableInterface $variable, VariantInterface $variant)
    {
        $variable->getVariants()->shouldBeCalled()->willReturn([$variant]);

        $this->beConstructedWith($variable);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\VariationBundle\Form\ChoiceList\VariantChoiceList');
    }
}
