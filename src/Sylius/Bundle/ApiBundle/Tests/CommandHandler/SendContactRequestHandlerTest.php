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

namespace Sylius\Bundle\ApiBundle\Tests\CommandHandler;

use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendContactRequestHandlerTest extends KernelTestCase
{
    use ProphecyTrait;
    use MailerAssertionsTrait;

    /** @test */
    public function it_sends_contact_request(): void
    {
        if ($this->isItSwiftmailerTestEnv()) {
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

        $sendContactRequest = new SendContactRequest('shopUser@example.com', 'hello!');
        $sendContactRequest->setChannelCode('CHANNEL_CODE');
        $sendContactRequest->setLocaleCode('en_US');

        $channel->getHostname()->willReturn('Channel.host');
        $channel->getContactEmail()->willReturn('shop@example.com');

        $channelRepository->findOneByCode('CHANNEL_CODE')->willReturn($channel->reveal());

        $sendContactEmailHandler = new SendContactRequestHandler(
            $emailSender,
            $channelRepository->reveal(),
        );

        $sendContactEmailHandler($sendContactRequest);

        self::assertEmailCount(1);
        $email = self::getMailerMessage();
        self::assertEmailAddressContains($email, 'To', 'shop@example.com');
        self::assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.contact_request.content', [], null, 'en_US'));
    }

    private function isItSwiftmailerTestEnv(): bool
    {
        $env = self::getContainer()->getParameter('kernel.environment');

        return $env === 'test_with_swiftmailer';
    }
}
