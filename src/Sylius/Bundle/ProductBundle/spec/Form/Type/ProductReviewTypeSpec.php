<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Form\Type;

use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReviewTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('dataClass', array('validation_group'), 'product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Form\Type\ProductReviewType');
    }

    function it_extends_review_type()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Form\Type\ReviewType');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_review');
    }
}
