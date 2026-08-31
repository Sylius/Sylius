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

namespace spec\Sylius\Bundle\CoreBundle\Mailer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Mailer\ResetPasswordEmailManagerInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Core\Model\AdminUserInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ResetPasswordEmailManagerSpec extends ObjectBehavior
{
    function let(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
    ): void {
        $this->beConstructedWith($emailSender, $router, $channelContext, false);
    }

    function it_implements_a_reset_password_email_manager_interface(): void
    {
        $this->shouldImplement(ResetPasswordEmailManagerInterface::class);
    }

    function it_sends_an_admin_reset_password_email_with_an_url_based_on_the_channel_hostname(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        AdminUserInterface $adminUser,
    ): void {
        $adminUser->getEmail()->willReturn('admin@example.com');
        $adminUser->getPasswordResetToken()->willReturn('token');

        $channelContext->getChannel()->willReturn($channel);
        $channel->getHostname()->willReturn('sylius.example.com');

        $router
            ->generate(
                'sylius_admin_render_password_reset',
                ['token' => 'token'],
                UrlGeneratorInterface::ABSOLUTE_PATH,
            )
            ->willReturn('/admin/forgotten-password/token')
        ;

        $emailSender
            ->send(
                'admin_password_reset',
                ['admin@example.com'],
                [
                    'adminUser' => $adminUser,
                    'localeCode' => 'en_US',
                    'resetUrl' => 'https://sylius.example.com/admin/forgotten-password/token',
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendAdminResetPasswordEmail($adminUser, 'en_US');
    }

    function it_sends_an_admin_reset_password_email_with_an_unsecured_url_when_configured_so(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        AdminUserInterface $adminUser,
    ): void {
        $this->beConstructedWith($emailSender, $router, $channelContext, true);

        $adminUser->getEmail()->willReturn('admin@example.com');
        $adminUser->getPasswordResetToken()->willReturn('token');

        $channelContext->getChannel()->willReturn($channel);
        $channel->getHostname()->willReturn('sylius.example.com');

        $router
            ->generate(
                'sylius_admin_render_password_reset',
                ['token' => 'token'],
                UrlGeneratorInterface::ABSOLUTE_PATH,
            )
            ->willReturn('/admin/forgotten-password/token')
        ;

        $emailSender
            ->send(
                'admin_password_reset',
                ['admin@example.com'],
                [
                    'adminUser' => $adminUser,
                    'localeCode' => 'en_US',
                    'resetUrl' => 'http://sylius.example.com/admin/forgotten-password/token',
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendAdminResetPasswordEmail($adminUser, 'en_US');
    }

    function it_sends_an_admin_reset_password_email_without_an_url_if_no_channel_can_be_resolved(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
        AdminUserInterface $adminUser,
    ): void {
        $adminUser->getEmail()->willReturn('admin@example.com');

        $channelContext->getChannel()->willThrow(new ChannelNotFoundException());

        $router->generate(Argument::cetera())->shouldNotBeCalled();

        $emailSender
            ->send(
                'admin_password_reset',
                ['admin@example.com'],
                [
                    'adminUser' => $adminUser,
                    'localeCode' => 'en_US',
                    'resetUrl' => null,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendAdminResetPasswordEmail($adminUser, 'en_US');
    }

    function it_sends_an_admin_reset_password_email_without_an_url_if_the_channel_has_no_hostname(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        AdminUserInterface $adminUser,
    ): void {
        $adminUser->getEmail()->willReturn('admin@example.com');

        $channelContext->getChannel()->willReturn($channel);
        $channel->getHostname()->willReturn(null);

        $router->generate(Argument::cetera())->shouldNotBeCalled();

        $emailSender
            ->send(
                'admin_password_reset',
                ['admin@example.com'],
                [
                    'adminUser' => $adminUser,
                    'localeCode' => 'en_US',
                    'resetUrl' => null,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendAdminResetPasswordEmail($adminUser, 'en_US');
    }

    function it_sends_an_admin_reset_password_email_without_an_url_if_the_admin_route_is_not_registered(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        AdminUserInterface $adminUser,
    ): void {
        $adminUser->getEmail()->willReturn('admin@example.com');
        $adminUser->getPasswordResetToken()->willReturn('token');

        $channelContext->getChannel()->willReturn($channel);
        $channel->getHostname()->willReturn('sylius.example.com');

        $router
            ->generate(
                'sylius_admin_render_password_reset',
                ['token' => 'token'],
                UrlGeneratorInterface::ABSOLUTE_PATH,
            )
            ->willThrow(new RouteNotFoundException())
        ;

        $emailSender
            ->send(
                'admin_password_reset',
                ['admin@example.com'],
                [
                    'adminUser' => $adminUser,
                    'localeCode' => 'en_US',
                    'resetUrl' => null,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendAdminResetPasswordEmail($adminUser, 'en_US');
    }

    function it_sends_a_reset_password_email(
        SenderInterface $emailSender,
        RouterInterface $router,
        ChannelContextInterface $channelContext,
        ChannelInterface $channel,
        UserInterface $user,
    ): void {
        $user->getEmail()->willReturn('customer@example.com');

        $channelContext->getChannel()->shouldNotBeCalled();
        $router->generate(Argument::cetera())->shouldNotBeCalled();

        $emailSender
            ->send(
                'password_reset',
                ['customer@example.com'],
                [
                    'user' => $user,
                    'localeCode' => 'en_US',
                    'channel' => $channel,
                ],
            )
            ->shouldBeCalled()
        ;

        $this->sendResetPasswordEmail($user, $channel, 'en_US');
    }
}
