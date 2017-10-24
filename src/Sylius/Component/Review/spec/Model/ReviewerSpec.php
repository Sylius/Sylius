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

namespace spec\Sylius\Component\Review\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Review\Model\ReviewerInterface;

final class ReviewerSpec extends ObjectBehavior
{
    function it_implements_reviewer_interface(): void
    {
        $this->shouldImplement(ReviewerInterface::class);
    }

    function it_has_an_email(): void
    {
        $this->setEmail('john.doe@example.com');
        $this->getEmail()->shouldReturn('john.doe@example.com');
    }

    function it_has_a_first_name(): void
    {
        $this->setFirstName('John');
        $this->getFirstName()->shouldReturn('John');
    }

    function it_has_a_last_name(): void
    {
        $this->setLastName('Doe');
        $this->getLastName()->shouldReturn('Doe');
    }
}
