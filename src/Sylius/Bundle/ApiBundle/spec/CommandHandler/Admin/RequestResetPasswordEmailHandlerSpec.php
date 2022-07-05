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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Admin;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Command\Admin\RequestResetPasswordEmail;
use Sylius\Bundle\ApiBundle\Command\Admin\SendResetPasswordEmail;
use Sylius\Calendar\Provider\DateTimeProviderInterface;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class RequestResetPasswordEmailHandlerSpec extends ObjectBehavior
{
    private const SAMPLE_EMAIL = 'admin@example.com';

    private const SAMPLE_LOCALE_CODE = 'en_US';

    public function let(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        DateTimeProviderInterface $calendar,
        MessageBusInterface $messageBus
    ): void {
        $this->beConstructedWith($userRepository, $generator, $calendar, $messageBus);
    }

    public function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    public function it_handles_request_for_password_reset_token(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        DateTimeProviderInterface $calendar,
        MessageBusInterface $messageBus,
        AdminUserInterface $adminUser
    ): void {
        $userRepository->findOneByEmail(self::SAMPLE_EMAIL)->willReturn($adminUser);

        $generator->generate()->willReturn('sometoken');

        $calendar->now()->willReturn(new \DateTime());

        $adminUser->getEmail()->willReturn(self::SAMPLE_EMAIL);
        $adminUser->getLocaleCode()->willReturn(self::SAMPLE_LOCALE_CODE);
        $adminUser->setPasswordResetToken('sometoken')->shouldBeCalledOnce();
        $adminUser->setPasswordRequestedAt(Argument::type(\DateTime::class))->shouldBeCalledOnce();

        $sendResetPasswordEmail = new SendResetPasswordEmail(self::SAMPLE_EMAIL, self::SAMPLE_LOCALE_CODE);

        $messageBus->dispatch(
            $sendResetPasswordEmail,
            [new DispatchAfterCurrentBusStamp()]
        )->willReturn(new Envelope($sendResetPasswordEmail))->shouldBeCalledOnce();

        $requestResetPasswordEmail = new RequestResetPasswordEmail(self::SAMPLE_EMAIL);
        $this($requestResetPasswordEmail);
    }

    public function it_throws_exception_while_handling_if_user_doesnt_exist(
        UserRepositoryInterface $userRepository
    ): void {
        $userRepository->findOneByEmail(self::SAMPLE_EMAIL)->willReturn(null);

        $requestResetPasswordEmail = new RequestResetPasswordEmail(self::SAMPLE_EMAIL);
        $this->shouldThrow(\InvalidArgumentException::class)->during('__invoke', [$requestResetPasswordEmail]);
    }
}
