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
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmail;
use Symfony\Component\Validator\Constraint;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class UniqueReviewerEmailSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(UniqueReviewerEmail::class);
    }

    function it_extends_constraint_class()
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_a_message()
    {
        $this->message->shouldReturn('sylius.review.author.already_exists');
    }

    function it_is_validate_by_unique_user_email_validator()
    {
        $this->validatedBy()->shouldReturn('sylius_unique_reviewer_email_validator');
    }

    function it_has_targets()
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }
}
