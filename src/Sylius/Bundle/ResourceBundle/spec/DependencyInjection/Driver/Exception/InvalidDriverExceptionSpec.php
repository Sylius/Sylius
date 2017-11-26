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

namespace spec\Sylius\Bundle\ResourceBundle\DependencyInjection\Driver\Exception;

use PhpSpec\ObjectBehavior;

final class InvalidDriverExceptionSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith('driver', 'className');
    }

    public function it_extends_exception(): void
    {
        $this->shouldHaveType(\Throwable::class);
    }

    public function it_has_a_message(): void
    {
        $this->getMessage()->shouldReturn('Driver "driver" is not supported by className.');
    }
}
