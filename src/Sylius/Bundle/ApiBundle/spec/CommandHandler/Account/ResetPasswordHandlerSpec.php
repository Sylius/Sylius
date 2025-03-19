<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Command\Account\ResetPassword;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;

final class ResetPasswordHandlerSpec extends ObjectBehavior
{
    use MessageHandlerAttributeTrait;

    function let(UserPasswordResetterInterface $userPasswordResetter): void
    {
        $this->beConstructedWith($userPasswordResetter);
    }

    function it_delegates_password_resetting(UserPasswordResetterInterface $userPasswordResetter): void
    {
        $userPasswordResetter->reset('TOKEN', 'newPassword')->shouldBeCalled();

        $this(new ResetPassword('TOKEN', 'newPassword', 'newPassword'));
    }
}
