<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler\Account;

use Sylius\Bundle\ApiBundle\Command\Account\RequestResetPasswordToken;
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

/** @experimental */
final class RequestResetPasswordTokenHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private MessageBusInterface $commandBus,
        private GeneratorInterface $generator,
    ) {
    }

    public function __invoke(RequestResetPasswordToken $command): void
    {
        $user = $this->userRepository->findOneByEmail($command->getEmail());
        Assert::notNull($user);

        $user->setPasswordResetToken($this->generator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

        $this->commandBus->dispatch(
            new SendResetPasswordEmail(
                $command->getEmail(),
                $command->getChannelCode(),
                $command->getLocaleCode(),
            ),
            [new DispatchAfterCurrentBusStamp()],
        );
    }
}
