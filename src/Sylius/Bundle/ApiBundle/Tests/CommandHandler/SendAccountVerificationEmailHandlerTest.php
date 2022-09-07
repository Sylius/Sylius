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
use Sylius\Bundle\ApiBundle\Command\Account\SendAccountVerificationEmail;
use Sylius\Bundle\ApiBundle\CommandHandler\Account\SendAccountVerificationEmailHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendAccountVerificationEmailHandlerTest extends KernelTestCase
{
    use MailerAssertionsTrait;

    /** @test */
    public function it_sends_account_verification_token_email(): void
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

        $sendAccountVerificationEmailHandler = new SendAccountVerificationEmailHandler(
            $userRepository->reveal(),
            $channelRepository->reveal(),
            $emailSender,
        );

        $sendAccountVerificationEmailHandler(
            new SendAccountVerificationEmail(
                'user@example.com',
                'en_US',
                'CHANNEL_CODE',
            ),
        );

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'To', 'user@example.com');
        $this->assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.verification_token.message', [], null, 'en_US'));
    }

    private function isItSwiftmailerTestEnv(): bool
    {
        $env = $this->getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
