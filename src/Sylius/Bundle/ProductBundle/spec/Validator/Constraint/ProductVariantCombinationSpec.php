<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ProductBundle\Validator\Constraint;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

final class ProductVariantCombinationSpec extends ObjectBehavior
{
    function it_is_a_validation_constraint(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_is_class_constraint(): void
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }
}
