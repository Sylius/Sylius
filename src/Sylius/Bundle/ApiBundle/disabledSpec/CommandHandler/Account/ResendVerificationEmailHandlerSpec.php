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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Account;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Account\ResendVerificationEmail;
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountVerificationEmail;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Sylius\Component\User\Security\Generator\GeneratorInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class ResendVerificationEmailHandlerSpec extends ObjectBehavior
{
    function let(
        UserRepositoryInterface $userRepository,
        GeneratorInterface $generator,
        MessageBusInterface $messageBus,
    ): void {
        $this->beConstructedWith($userRepository, $generator, $messageBus);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_throws_exception_if_shop_user_does_not_exist(UserRepositoryInterface $userRepository): void
    {
        $userRepository->find(42)->willReturn(null);

        $resendVerificationEmail = new ResendVerificationEmail();
        $resendVerificationEmail->setChannelCode('WEB');
        $resendVerificationEmail->setLocaleCode('en_US');
        $resendVerificationEmail->setShopUserId(42);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$resendVerificationEmail])
        ;
    }

    function it_handles_request_for_resend_verification_email(
        UserRepositoryInterface $userRepository,
        ShopUserInterface $shopUser,
        GeneratorInterface $generator,
        MessageBusInterface $messageBus,
        CustomerInterface $customer,
    ): void {
        $userRepository->find(42)->willReturn($shopUser);
        $shopUser->getCustomer()->willReturn($customer);
        $customer->getEmail()->willReturn('test@email.com');

        $generator->generate()->willReturn('TOKEN');
        $shopUser->setEmailVerificationToken('TOKEN')->shouldBeCalled();

        $sendAccountVerificationEmail = new SendAccountVerificationEmail('test@email.com', 'en_US', 'WEB');

        $messageBus->dispatch(
            $sendAccountVerificationEmail,
            [new DispatchAfterCurrentBusStamp()],
        )->willReturn(new Envelope($sendAccountVerificationEmail))->shouldBeCalled();

        $resendVerificationEmail = new ResendVerificationEmail();
        $resendVerificationEmail->setChannelCode('WEB');
        $resendVerificationEmail->setLocaleCode('en_US');
        $resendVerificationEmail->setShopUserId(42);

        $this($resendVerificationEmail);
    }
}
