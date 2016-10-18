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
use Sylius\Bundle\ProductBundle\Validator\Constraint\ProductVariantCombination;
use Symfony\Component\Validator\Constraint;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class ProductVariantCombinationSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductVariantCombination::class);
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
