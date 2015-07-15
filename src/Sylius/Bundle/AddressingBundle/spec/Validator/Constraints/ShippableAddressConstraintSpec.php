<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class ShippableAddressConstraintSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\AddressingBundle\Validator\Constraints\ShippableAddressConstraint');
    }

    public function it_has_targets()
    {
        $this->getTargets()->shouldReturn('class');
    }

    public function it_is_validated_by()
    {
        $this->validatedBy()->shouldReturn('sylius_shippable_address_validator');
    }
}
