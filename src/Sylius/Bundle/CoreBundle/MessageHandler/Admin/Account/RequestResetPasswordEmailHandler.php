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

namespace Sylius\Bundle\CoreBundle\MessageHandler\Admin\Account;

use Sylius\Bundle\CoreBundle\Message\Admin\Account\RequestResetPasswordEmail;
use Sylius\Bundle\CoreBundle\Message\Admin\Account\SendResetPasswordEmail;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RequestResetPasswordEmailHandler implements MessageHandlerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private GeneratorInterface $generator,
        private DateTimeProviderInterface $calendar,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(RequestResetPasswordEmail $requestResetPasswordEmail)
    {
        /** @var AdminUserInterface|null $adminUser */
        $adminUser = $this->userRepository->findOneByEmail($requestResetPasswordEmail->email);
        if (null === $adminUser) {
            return;
        }

        $adminUser->setPasswordResetToken($this->generator->generate());
        $adminUser->setPasswordRequestedAt($this->calendar->now());

        $this->commandBus->dispatch(
            new SendResetPasswordEmail($adminUser->getEmail(), $adminUser->getLocaleCode()),
            [new DispatchAfterCurrentBusStamp()],
        );
    }
}
