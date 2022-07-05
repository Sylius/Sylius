<?php

/*
 *  This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\CommandHandler\Admin;

use Sylius\Bundle\ApiBundle\Command\Admin\RequestResetPasswordEmail;
use Sylius\Bundle\ApiBundle\Command\Admin\SendResetPasswordEmail;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Webmozart\Assert\Assert;

/** @experimental */
final class RequestResetPasswordEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GeneratorInterface $generator,
        private DateTimeProviderInterface $calendar,
        private MessageBusInterface $commandBus
    ) {
    }

    public function __invoke(RequestResetPasswordEmail $requestResetPasswordEmail)
    {
        /** @var AdminUserInterface $adminUser */
        $adminUser = $this->userRepository->findOneByEmail($requestResetPasswordEmail->getEmail());
        Assert::notNull($adminUser);

        $adminUser->setPasswordResetToken($this->generator->generate());
        $adminUser->setPasswordRequestedAt($this->calendar->now());

        $this->commandBus->dispatch(
            new SendResetPasswordEmail($adminUser->getEmail(), $adminUser->getLocaleCode()),
            [new DispatchAfterCurrentBusStamp()]
        );
    }
}
