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
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountRegistrationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountRegistrationEmailHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\Services\EmailChecker;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendAccountRegistrationEmailHandlerTest extends KernelTestCase
{
    use MailerAssertionsTrait;

    /** @test */
    public function it_sends_account_registration_email(): void
    {
        if ($this->isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('Test is relevant only for the environment without swiftmailer');
        }

        $container = self::bootKernel()->getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

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
        $user->getEmailVerificationToken()->willReturn('token');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel->reveal());
        $userRepository->findOneByEmail('user@example.com')->willReturn($user->reveal());

        $sendAccountRegistrationEmailHandler = new SendAccountRegistrationEmailHandler(
            $userRepository->reveal(),
            $channelRepository->reveal(),
            $emailSender,
        );

        $sendAccountRegistrationEmailHandler(
            new SendAccountRegistrationEmail(
                'user@example.com',
                'en_US',
                'CHANNEL_CODE',
            ),
        );

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'To', 'user@example.com');
        $this->assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.user_registration.start_shopping', [], null, 'en_US'));
    }

    /** @test */
    public function it_sends_account_registration_email_with_swiftmailer(): void
    {
        if (!$this->isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('Test is relevant only for the environment with swiftmailer');
        }

        $container = self::bootKernel()->getContainer();

        /** @var Filesystem $filesystem */
        $filesystem = $container->get('test.filesystem.public');

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
        $user->getEmailVerificationToken()->willReturn('token');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel->reveal());
        $userRepository->findOneByEmail('user@example.com')->willReturn($user->reveal());

        $sendAccountRegistrationEmailHandler = new SendAccountRegistrationEmailHandler(
            $userRepository->reveal(),
            $channelRepository->reveal(),
            $emailSender,
        );

        $sendAccountRegistrationEmailHandler(
            new SendAccountRegistrationEmail(
                'user@example.com',
                'en_US',
                'CHANNEL_CODE',
            ),
        );

        self::assertSame(1, $emailChecker->countMessagesTo('user@example.com'));
        self::assertTrue($emailChecker->hasMessageTo(
            $translator->trans('sylius.email.user_registration.start_shopping', [], null, 'en_US'),
            'user@example.com',
        ));
    }

    private function isItSwiftmailerTestEnv(): bool
    {
        $env = $this->getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
