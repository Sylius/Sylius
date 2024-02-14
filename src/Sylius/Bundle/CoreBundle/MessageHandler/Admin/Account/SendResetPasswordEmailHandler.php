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

use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\SendResetPasswordEmail;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class SendResetPasswordEmailHandler implements MessageHandlerInterface
{
    /**
     * @param UserRepositoryInterface<AdminUserInterface> $userRepository
     */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private ResetPasswordEmailManagerInterface $resetPasswordEmailManager,
    ) {
    }

    public function __invoke(SendResetPasswordEmail $sendResetPasswordEmail): void
    {
        $adminUser = $this->userRepository->findOneByEmail($sendResetPasswordEmail->email);
        Assert::notNull($adminUser);

        $this->resetPasswordEmailManager->sendAdminResetPasswordEmail($adminUser, $sendResetPasswordEmail->localeCode);
    }
}
