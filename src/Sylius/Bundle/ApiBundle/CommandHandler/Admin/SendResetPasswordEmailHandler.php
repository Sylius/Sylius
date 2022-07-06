<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler\Admin;

use Sylius\Bundle\ApiBundle\Command\Admin\SendResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** @experimental */
final class SendResetPasswordEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private SenderInterface $sender
    ) {
    }

    public function __invoke(SendResetPasswordEmail $sendResetPasswordEmail): void
    {
        $adminUser = $this->userRepository->findOneByEmail($sendResetPasswordEmail->email);
        Assert::notNull($adminUser);

        $this->sender->send(
            Emails::ADMIN_PASSWORD_RESET,
            [$sendResetPasswordEmail->email],
            [
                'adminUser' => $adminUser,
                'localeCode' => $sendResetPasswordEmail->localeCode,
            ]
        );
    }
}
