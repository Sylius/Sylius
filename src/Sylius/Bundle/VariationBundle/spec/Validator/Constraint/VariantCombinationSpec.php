<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\VariationBundle\Validator\Constraint;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class VariantCombinationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\VariationBundle\Validator\Constraint\VariantCombination');
    }

    function it_is_a_validation_constraint()
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_is_class_constraint()
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }
}
