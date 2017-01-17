<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntity;
use Symfony\Component\Validator\Constraint;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
final class HasEnabledEntitySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(HasEnabledEntity::class);
    }

    public function it_is_a_constraint()
    {
        $this->shouldHaveType(Constraint::class);
    }

    public function it_has_validator()
    {
        $this->validatedBy()->shouldReturn('sylius_has_enabled_entity');
    }

    public function it_has_a_target()
    {
        $this->getTargets()->shouldReturn('class');
    }
}
