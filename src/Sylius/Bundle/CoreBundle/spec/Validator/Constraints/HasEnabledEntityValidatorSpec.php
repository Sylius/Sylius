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

use Doctrine\Common\Persistence\ManagerRegistry;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class HasEnabledEntityValidatorSpec extends ObjectBehavior
{
    public function let(ManagerRegistry $registry)
    {
        $this->beConstructedWith($registry);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator');
    }

    public function it_is_a_constraint_validator()
    {
        $this->shouldHaveType(ConstraintValidator::class);
    }
}
