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

namespace Sylius\Component\User\Security\Checker;

use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface as SymfonyUserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class EnabledUserChecker implements UserCheckerInterface
{
    public function __construct(private ?TranslatorInterface $translator = null)
    {
        if (null === $this->translator) {
            trigger_deprecation(
                'sylius/user',
                '2.3',
                'Not passing a "%s" to "%s" is deprecated and will be required in Sylius 3.0.',
                TranslatorInterface::class,
                self::class,
            );
        }
    }

    public function checkPreAuth(SymfonyUserInterface $user): void
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user->isEnabled()) {
            $exception = null !== $this->translator
                ? new CustomUserMessageAccountStatusException(
                    $this->translator->trans('sylius.user.account_disabled', [], 'validators'),
                )
                : new DisabledException('User account is disabled.');
            $exception->setUser($user);

            throw $exception;
        }
    }

    public function checkPostAuth(SymfonyUserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
