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

namespace spec\Sylius\Behat;

use PhpSpec\ObjectBehavior;

final class NotificationTypeSpec extends ObjectBehavior
{
    public function it_initialize_with_success_value(): void
    {
        $this->beConstructedThrough('success');
        $this->__toString()->shouldReturn('success');
    }

    public function it_initialize_with_failure_value(): void
    {
        $this->beConstructedThrough('failure');
        $this->__toString()->shouldReturn('failure');
    }
}
