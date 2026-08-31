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

namespace Sylius\Bundle\CoreBundle\Mailer;

use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ResetPasswordEmailManager implements ResetPasswordEmailManagerInterface
{
    public function __construct(
        private SenderInterface $emailSender,
        private RouterInterface $router,
        private ChannelContextInterface $channelContext,
        private bool $unsecuredUrls = false,
    ) {
    }

    public function sendAdminResetPasswordEmail(UserInterface $user, string $localCode): void
    {
        $this->emailSender->send(
            code: Emails::ADMIN_PASSWORD_RESET,
            recipients: [$user->getEmail()],
            data: [
                'adminUser' => $user,
                'localeCode' => $localCode,
                'resetUrl' => $this->generateAdminResetPasswordUrl($user),
            ],
        );
    }

    public function sendResetPasswordEmail(UserInterface $user, ChannelInterface $channel, string $localCode): void
    {
        $this->emailSender->send(
            code: Emails::PASSWORD_RESET,
            recipients: [$user->getEmail()],
            data: [
                'user' => $user,
                'localeCode' => $localCode,
                'channel' => $channel,
            ],
        );
    }

    private function generateAdminResetPasswordUrl(UserInterface $user): ?string
    {
        try {
            $hostname = $this->channelContext->getChannel()->getHostname();
        } catch (ChannelNotFoundException) {
            return null;
        }

        if (null === $hostname) {
            return null;
        }

        try {
            $path = $this->router->generate(
                'sylius_admin_render_password_reset',
                ['token' => (string) $user->getPasswordResetToken()],
                UrlGeneratorInterface::ABSOLUTE_PATH,
            );
        } catch (InvalidParameterException|RouteNotFoundException) {
            return null;
        }

        return ($this->unsecuredUrls ? 'http://' : 'https://') . $hostname . $path;
    }
}
