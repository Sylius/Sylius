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

namespace spec\Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Command\Admin\Account\ResetPassword;
use Sylius\Bundle\CoreBundle\CommandHandler\Admin\Account\ResetPasswordHandler;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

final class ResetPasswordHandlerSpec extends ObjectBehavior
{
    function let(UserPasswordResetterInterface $userPasswordResetter): void
    {
        $this->beConstructedWith($userPasswordResetter);
    }

    function it_is_a_message_handler(): void
    {
        $messageHandlerAttributes = (new \ReflectionClass(ResetPasswordHandler::class))
            ->getAttributes(AsMessageHandler::class);

        Assert::count($messageHandlerAttributes, 1);
    }

    function it_delegates_password_resetting(UserPasswordResetterInterface $userPasswordResetter): void
    {
        $userPasswordResetter->reset('TOKEN', 'newPassword')->shouldBeCalled();

        $this(new ResetPassword('TOKEN', 'newPassword'));
    }
}
