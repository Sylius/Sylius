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

namespace spec\Sylius\Bundle\CoreBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Symfony\Component\Validator\Constraint;

final class UniqueReviewerEmailSpec extends ObjectBehavior
{
    function it_extends_constraint_class(): void
    {
        $this->shouldHaveType(Constraint::class);
    }

    function it_has_a_message(): void
    {
        $this->message->shouldReturn('sylius.review.author.already_exists');
    }

    function it_is_validate_by_unique_user_email_validator(): void
    {
        $this->validatedBy()->shouldReturn('sylius_unique_reviewer_email_validator');
    }

    function it_has_targets(): void
    {
        $this->getTargets()->shouldReturn(Constraint::CLASS_CONSTRAINT);
    }
}
