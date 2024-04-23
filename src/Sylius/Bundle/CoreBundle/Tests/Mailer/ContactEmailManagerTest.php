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
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Test\SwiftmailerAssertionTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ContactEmailManagerTest extends KernelTestCase
{
    use ProphecyTrait;
    use SwiftmailerAssertionTrait;

    /** @test */
    public function it_sends_contact_request(): void
    {
        if (self::isSwiftmailerTestEnv()) {
            $this->markTestSkipped('Test is relevant only for the environment without swiftmailer');
        }

        $container = self::getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        $emailSender = $container->get('sylius.email_sender');

        /** @var ChannelRepositoryInterface|ObjectProphecy $channelRepository */
        $channelRepository = $this->prophesize(ChannelRepositoryInterface::class);
        /** @var ChannelInterface|ObjectProphecy $channel */
        $channel = $this->prophesize(ChannelInterface::class);

        $channel->getHostname()->willReturn('Channel.host');
        $channel->getContactEmail()->willReturn('shop@example.com');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel->reveal());

        $contactEmailManager = new ContactEmailManager($emailSender);

        $contactEmailManager->sendContactRequest(
            ['email' => 'shop@example.com', 'message' => 'Hello contact request!'],
            ['shop@example.com'],
            $channel->reveal(),
            'en_US',
        );

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', 'shop@example.com');
        self::assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.contact_request.content', [], null, 'en_US'));
    }
}
