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
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\CommandHandler\SendContactRequestHandler;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendContactRequestHandlerTest extends KernelTestCase
{
    use MailerAssertionsTrait;

    /** @test */
    public function it_sends_contact_request(): void
    {
        $container = self::bootKernel()->getContainer();

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

        $this->assertEmailCount(1);
        $email = $this->getMailerMessage();
        $this->assertEmailAddressContains($email, 'To', 'shop@example.com');
        $this->assertEmailHtmlBodyContains($email, $translator->trans('sylius.email.contact_request.content', [], null, 'en_US'));
    }
}
