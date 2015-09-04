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

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class HasEnabledEntitySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntity');
    }

    public function it_is_a_contraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
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
