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

namespace spec\Sylius\Component\Mailer\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\Email;

final class EmailFactorySpec extends ObjectBehavior
{
    function it_implements_email_factory_interface(): void
    {
        $this->shouldImplement(EmailFactoryInterface::class);
    }

    function it_creates_new_email(): void
    {
        $this->createNew()->shouldHaveType(Email::class);
    }
}
