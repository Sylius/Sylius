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

namespace Sylius\Bundle\ApiBundle\CommandHandler\Account;

use Sylius\Bundle\ApiBundle\Command\Account\ChangeShopUserPassword;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\PasswordUpdaterInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class ChangeShopUserPasswordHandler implements MessageHandlerInterface
{
    public function __construct(
        private PasswordUpdaterInterface $passwordUpdater,
        private UserRepositoryInterface $userRepository,
    ) {
    }

    public function __invoke(ChangeShopUserPassword $changeShopUserPassword): void
    {
        if ($changeShopUserPassword->confirmNewPassword !== $changeShopUserPassword->newPassword) {
            throw new \InvalidArgumentException('Passwords do not match.');
        }

        /** @var ShopUserInterface|null $user */
        $user = $this->userRepository->find($changeShopUserPassword->getShopUserId());

        Assert::notNull($user);

        $user->setPlainPassword($changeShopUserPassword->newPassword);

        $this->passwordUpdater->updatePassword($user);
    }
}
