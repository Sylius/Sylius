<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Product\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Attribute\Model\Attribute;
use Sylius\Product\Model\AttributeInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AttributeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Product\Model\Attribute');
    }

    function it_extends_Sylius_attribute_model()
    {
        $this->shouldImplement(Attribute::class);
    }

    function it_implements_Sylius_product_attribute_interface()
    {
        $this->shouldImplement(AttributeInterface::class);
    }
}
