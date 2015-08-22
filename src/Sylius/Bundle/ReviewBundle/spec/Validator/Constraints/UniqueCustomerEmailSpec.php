<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ReviewBundle\Validator\Constraints;

use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class UniqueCustomerEmailSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ReviewBundle\Validator\Constraints\UniqueCustomerEmail');
    }

    function it_extends_contraint_class()
    {
        $this->shouldHaveType('Symfony\Component\Validator\Constraint');
    }

    function it_has_message()
    {
        $this->message->shouldReturn('sylius.review.author.already_exists');
    }

    function it_is_validate_by_unique_customer_email_validator()
    {
        $this->validatedBy()->shouldReturn('unique_customer_email_validator');
    }

    function it_has_targets()
    {
        $this->getTargets()->shouldReturn('class');
    }
}
