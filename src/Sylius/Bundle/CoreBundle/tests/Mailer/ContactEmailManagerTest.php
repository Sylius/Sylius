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

namespace Tests\Sylius\Bundle\CoreBundle\Mailer;

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManager;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AllowMockObjectsWithoutExpectations]
final class ContactEmailManagerTest extends KernelTestCase
{
    #[Test]
    public function it_sends_contact_request(): void
    {
        $container = self::getContainer();

        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        $emailSender = $container->get('sylius.email_sender');

        /** @var ChannelRepositoryInterface&MockObject $channelRepository */
        $channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        /** @var ChannelInterface&MockObject $channel */
        $channel = $this->createMock(ChannelInterface::class);

        $channel->method('getHostname')->willReturn('Channel.host');
        $channel->method('getContactEmail')->willReturn('shop@example.com');

        $channelRepository->method('findOneByCode')->with('CHANNEL_CODE')->willReturn($channel);

        $contactEmailManager = new ContactEmailManager($emailSender);

        $contactEmailManager->sendContactRequest(
            ['email' => 'shop@example.com', 'message' => 'Hello contact request!'],
            ['shop@example.com'],
            $channel,
            'en_US',
        );

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', 'shop@example.com');
        self::assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.contact_request.content', [], null, 'en_US'));
    }
}
