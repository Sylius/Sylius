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

namespace spec\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

final class InStockSpec extends ObjectBehavior
{
    function it_is_a_constraint(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_validator(): void
    {
        $this->validatedBy()->shouldReturn('sylius_in_stock');
    }

    function it_has_a_target(): void
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }
}
