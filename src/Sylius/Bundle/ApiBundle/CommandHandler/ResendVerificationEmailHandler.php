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

use Sylius\Bundle\ApiBundle\Command\ResendVerificationEmail;
use Sylius\Bundle\ApiBundle\Command\SendAccountVerificationEmail;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

/** @experimental  */
final class ResendVerificationEmailHandler implements MessageHandlerInterface
{
    private UserRepositoryInterface $shopUserRepository;

    private GeneratorInterface $tokenGenerator;

    private MessageBusInterface $commandBus;

    public function __construct(
        UserRepositoryInterface $shopUserRepository,
        GeneratorInterface $tokenGenerator,
        MessageBusInterface $commandBus
    ) {
        $this->shopUserRepository = $shopUserRepository;
        $this->tokenGenerator = $tokenGenerator;
        $this->commandBus = $commandBus;
    }

    public function __invoke(ResendVerificationEmail $command): void
    {
        /** @var UserInterface|null $user */
        Assert::notNull($user = $this->shopUserRepository->findOneByEmail($command->email));

        $token = $this->tokenGenerator->generate();
        $user->setEmailVerificationToken($token);

        $this->commandBus->dispatch(new SendAccountVerificationEmail(
            $command->email,
            $command->localeCode,
            $command->channelCode
        ), [new DispatchAfterCurrentBusStamp()]);
    }
}
