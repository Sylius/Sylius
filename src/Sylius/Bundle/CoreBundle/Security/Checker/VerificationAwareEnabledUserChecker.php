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
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;

final readonly class VerificationAwareEnabledUserChecker implements UserCheckerInterface
{
    public function __construct(
        private UserCheckerInterface $decorated,
        private ChannelContextInterface $channelContext,
    ) {
    }

    public function checkPreAuth(SymfonyUserInterface $user): void
    {
        if ($this->isPendingVerification($user)) {
            return;
        }

        $this->decorated->checkPreAuth($user);
    }

    public function checkPostAuth(SymfonyUserInterface $user): void
    {
        $this->decorated->checkPostAuth($user);
    }

    private function isPendingVerification(SymfonyUserInterface $user): bool
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        $channel = $this->channelContext->getChannel();

        return $channel instanceof ChannelInterface &&
            $channel->isAccountVerificationRequired() &&
            null === $user->getVerifiedAt();
    }
}
