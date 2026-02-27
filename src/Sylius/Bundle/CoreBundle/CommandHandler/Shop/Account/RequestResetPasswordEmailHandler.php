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

namespace Sylius\Bundle\CoreBundle\CommandHandler\Shop\Account;

use Sylius\Bundle\CoreBundle\Command\Shop\Account\RequestResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Command\Shop\Account\SendResetPasswordEmail;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

#[AsMessageHandler]
final readonly class RequestResetPasswordEmailHandler
{
    /** @param UserRepositoryInterface<UserInterface> $userRepository */
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GeneratorInterface $generator,
        private ClockInterface $clock,
        private MessageBusInterface $commandBus,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
    ) {
    }

    public function __invoke(RequestResetPasswordEmail $requestResetPasswordEmail): void
    {
        /** @var UserInterface|null $user */
        $user = $this->userRepository->findOneByEmail($requestResetPasswordEmail->email);
        if (null === $user || !$user->isEnabled()) {
            return;
        }

        $user->setPasswordResetToken($this->generator->generate());
        $user->setPasswordRequestedAt($this->clock->now());

        $this->commandBus->dispatch(
            new SendResetPasswordEmail(
                $user->getEmail(),
                $this->channelContext->getChannel()->getCode(),
                $this->localeContext->getLocaleCode(),
            ),
            [new DispatchAfterCurrentBusStamp()],
        );
    }
}
