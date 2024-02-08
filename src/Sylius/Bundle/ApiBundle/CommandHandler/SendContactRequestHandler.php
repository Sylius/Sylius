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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\CoreBundle\Mailer\Emails;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Mailer\Sender\SenderInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** experimental */
final class SendContactRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private SenderInterface $sender,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    public function __invoke(SendContactRequest $command): void
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelRepository->findOneByCode($command->getChannelCode());
        Assert::notNull($channel);

        $email = $command->getEmail();
        Assert::notNull($email);

        $this->sender->send(
            Emails::CONTACT_REQUEST,
            [$channel->getContactEmail()],
            [
                'data' => ['message' => $command->getMessage(), 'email' => $email],
                'channel' => $channel,
                'localeCode' => $command->getLocaleCode(),
            ],
            [],
            [$email],
        );
    }
}
