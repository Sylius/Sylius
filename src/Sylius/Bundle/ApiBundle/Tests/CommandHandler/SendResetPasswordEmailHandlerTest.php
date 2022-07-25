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

namespace Sylius\Bundle\ApiBundle\Tests\CommandHandler;

use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\Command\Account\SendResetPasswordEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendResetPasswordEmailHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\EmailChecker;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendResetPasswordEmailHandlerTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_sends_password_reset_token_email(): void
    {
        $container = self::bootKernel()->getContainer();

        /** @var Filesystem $filesystem */
        $filesystem = $container->get('filesystem');

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var EmailChecker $emailChecker */
        $emailChecker = $container->get('sylius.behat.email_checker');

        $filesystem->remove($emailChecker->getSpoolDirectory());

        $emailSender = $container->get('sylius.email_sender');

        /** @var ChannelRepositoryInterface|ObjectProphecy $channelRepository */
        $channelRepository = $this->prophesize(ChannelRepositoryInterface::class);
        /** @var UserRepositoryInterface|ObjectProphecy $userRepository */
        $userRepository = $this->prophesize(UserRepositoryInterface::class);
        /** @var ChannelInterface|ObjectProphecy $channel */
        $channel = $this->prophesize(ChannelInterface::class);
        /** @var UserInterface|ObjectProphecy $user */
        $user = $this->prophesize(UserInterface::class);

        $user->getUsername()->willReturn('username');
        $user->getPasswordResetToken()->willReturn('token');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel->reveal());
        $userRepository->findOneByEmail('user@example.com')->willReturn($user->reveal());

        $resetPasswordEmailHandler = new SendResetPasswordEmailHandler(
            $emailSender,
            $channelRepository->reveal(),
            $userRepository->reveal(),
        );

        $resetPasswordEmailHandler(new SendResetPasswordEmail(
            'user@example.com',
            'CHANNEL_CODE',
            'en_US',
        ));

        self::assertSame(1, $emailChecker->countMessagesTo('user@example.com'));
        self::assertTrue($emailChecker->hasMessageTo(
            $translator->trans('sylius.email.password_reset.to_reset_your_password_token', [], null, 'en_US'),
            'user@example.com',
        ));
    }
}
