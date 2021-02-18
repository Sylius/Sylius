<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\ResetPassword;
use Sylius\Bundle\ApiBundle\Event\ResetPasswordRequested;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

class ResetPasswordHandler
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

    public function __invoke(ResetPassword $command): void
    {
        $user = $this->userRepository->findOneByEmail($command->getEmail());
        Assert::notNull($user);

        $user->setPasswordResetToken($this->generator->generate());
        $user->setPasswordRequestedAt(new \DateTime());

        $this->eventBus->dispatch(
            new ResetPasswordRequested($command->getEmail(), $command->getChannelCode(), $command->getLocaleCode()),
            [new DispatchAfterCurrentBusStamp()
        ]);
    }
}
