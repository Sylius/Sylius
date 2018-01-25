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

namespace spec\Sylius\Bundle\AddressingBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\AddressingBundle\Validator\Constraints\ProvinceAddressConstraint;

final class ProvinceAddressConstraintSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ProvinceAddressConstraint::class);
    }

    function it_has_targets(): void
    {
        $this->getTargets()->shouldReturn('class');
    }

    function it_is_validated_by(): void
    {
        $this->validatedBy()->shouldReturn('sylius_province_address_validator');
    }
}
