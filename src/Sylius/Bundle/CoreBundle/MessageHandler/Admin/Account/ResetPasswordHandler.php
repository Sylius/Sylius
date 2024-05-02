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

namespace Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account;

use Sylius\Bundle\CoreBundle\Message\Admin\Account\ResetPassword;
use Sylius\Bundle\CoreBundle\Security\UserPasswordResetterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ResetPasswordHandler implements MessageHandlerInterface
{
    public function __construct(private UserPasswordResetterInterface $userPasswordResetter)
    {
    }

    public function __invoke(ResetPassword $command): void
    {
        $this->userPasswordResetter->reset($command->token, $command->newPassword);
    }
}
