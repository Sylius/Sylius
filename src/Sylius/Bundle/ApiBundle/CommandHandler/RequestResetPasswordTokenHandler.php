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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\RequestResetPasswordToken;
use Sylius\Bundle\ApiBundle\Command\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\Event\ResetPasswordRequested;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

final class RequestResetPasswordTokenHandler
{
    /** @var UserRepositoryInterface */
    private $userRepository;

    /** @var MessageBusInterface */
    private $eventBus;

    /** @var GeneratorInterface */
    private $generator;

    public function __construct(
        UserRepositoryInterface $userRepository,
        MessageBusInterface $eventBus,
        GeneratorInterface $generator
    ) {
        $this->userRepository = $userRepository;
        $this->eventBus = $eventBus;
        $this->generator = $generator;
    }

    public function __invoke(RequestResetPasswordToken $command): void
    {
        $user = $this->userRepository->findOneByEmail($command->getEmail());
        Assert::notNull($user);

        $user->setPasswordResetToken($this->generator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

        $this->eventBus->dispatch(
            new SendResetPasswordEmail(
                $command->getEmail(),
                $command->getChannelCode(),
                $command->getLocaleCode()
            )
        );
    }
}
