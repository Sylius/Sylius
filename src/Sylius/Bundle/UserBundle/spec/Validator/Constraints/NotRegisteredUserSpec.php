<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\UserBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class NotRegisteredUserSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\UserBundle\Validator\Constraints\NotRegisteredUser');
    }

    function it_extends_symfony_constraint()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    function it_is_validated_by_not_registered_user_validator()
    {
        $this->validatedBy()->shouldReturn('not_registered_user_validator');
    }

    function it_can_be_set_only_on_property()
    {
        $this->getTargets()->shouldReturn('property');
    }
}
