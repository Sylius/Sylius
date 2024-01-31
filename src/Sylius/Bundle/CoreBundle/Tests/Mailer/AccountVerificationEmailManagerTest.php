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

namespace Sylius\Bundle\CoreBundle\Tests\Mailer;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\CoreBundle\Mailer\AccountVerificationEmailManagerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\User\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class AccountVerificationEmailManagerTest extends KernelTestCase
{
    use ProphecyTrait;

    /** @test */
    public function it_sends_account_verification_token_email(): void
    {
        if ($this->isItSwiftmailerTestEnv()) {
            $this->markTestSkipped('This test should be executed only outside of test_with_swiftmailer environment');
        }

        $container = self::getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        $accountVerificationEmailManager = $container->get(AccountVerificationEmailManagerInterface::class);

        /** @var UserInterface|ObjectProphecy $user */
        $user = $this->prophesize(UserInterface::class);
        $user->getUsername()->willReturn('username');
        $user->getEmail()->willReturn('customer@example.com');
        $user->getEmailVerificationToken()->willReturn('token');
        /** @var ChannelInterface|ObjectProphecy $channel */
        $channel = $this->prophesize(ChannelInterface::class);

        $accountVerificationEmailManager->sendAccountVerificationEmail(
            $user->reveal(),
            $channel->reveal(),
            'en_US',
        );

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', 'customer@example.com');
        self::assertEmailHtmlBodyContains(
            $email,
            $translator->trans('sylius.email.verification_token.verify_your_email_address', [], null, 'en_US'),
        );
    }

    private function isItSwiftmailerTestEnv(): bool
    {
        $env = self::getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
