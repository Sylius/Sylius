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
use Sylius\Component\Core\Test\Services\EmailChecker;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SendContactRequestHandlerTest extends KernelTestCase
{
    /** @test */
    public function it_sends_contact_request(): void
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

        self::assertSame(1, $emailChecker->countMessagesTo('shop@example.com'));
        self::assertTrue($emailChecker->hasMessageTo(
            $translator->trans('sylius.email.contact_request.content', [], null, 'en_US'),
            'shop@example.com',
        ));
    }
}
