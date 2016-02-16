<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\Type;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ReviewBundle\Form\Type\ReviewType;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReviewTypeSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('dataClass', ['validation_group'], 'product');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Form\Type\ProductReviewType');
    }

    function it_extends_review_type()
    {
        $this->shouldHaveType(ReviewType::class);
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_product_review');
    }
}
