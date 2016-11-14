<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Mailer\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Factory\EmailFactory;
use Sylius\Component\Mailer\Factory\EmailFactoryInterface;
use Sylius\Component\Mailer\Model\Email;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class EmailFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(EmailFactory::class);
    }

    function it_implements_email_factory_interface()
    {
        $this->shouldImplement(EmailFactoryInterface::class);
    }

    function it_creates_new_email()
    {
        $this->createNew()->shouldHaveType(Email::class);
    }
}
