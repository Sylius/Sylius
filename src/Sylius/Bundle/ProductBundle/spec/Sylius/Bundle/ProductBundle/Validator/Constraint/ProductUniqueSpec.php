<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ProductBundle\Validator\Constraint;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductUniqueSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(array('property' => 'name'));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ProductBundle\Validator\Constraint\ProductUnique');
    }

    function it_is_a_validation_constraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    function it_is_class_constraint()
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }
}
