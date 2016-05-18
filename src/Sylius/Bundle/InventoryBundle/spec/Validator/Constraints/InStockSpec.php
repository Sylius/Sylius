<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

class InStockSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Validator\Constraints\InStock');
    }

    function it_is_a_constraint()
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_validator()
    {
        $this->validatedBy()->shouldReturn('sylius_in_stock');
    }

    function it_has_a_target()
    {
        $this->getTargets()->shouldReturn('class');
    }
}
