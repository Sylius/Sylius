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

namespace Sylius\Bundle\CoreBundle\Security\Checker;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

final readonly class EmailVerificationUserChecker implements UserCheckerInterface
{
    public function __construct(private ChannelContextInterface $channelContext)
    {
    }

    public function checkPreAuth(SymfonyUserInterface $user): void
    {
    }

    public function checkPostAuth(SymfonyUserInterface $user): void
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        $channel = $this->channelContext->getChannel();

        if (!$channel instanceof ChannelInterface || !$channel->isAccountVerificationRequired()) {
            return;
        }

        if (null === $user->getVerifiedAt()) {
            $exception = new CustomUserMessageAccountStatusException('sylius.user.email_not_verified');
            $exception->setUser($user);

            throw $exception;
        }
    }
}
