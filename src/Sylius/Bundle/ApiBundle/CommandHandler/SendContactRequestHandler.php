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
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\CoreBundle\Mailer\ContactEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class SendContactRequestHandler
{
    /**
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private ContactEmailManagerInterface $contactEmailManager,
    ) {
    }

    public function __invoke(SendContactRequest $command): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = $this->channelRepository->findOneByCode($command->channelCode);

        if ($channel === null) {
            throw new ChannelNotFoundException($command->channelCode);
        }

        $email = $command->email;
        Assert::notNull($email);

        $this->contactEmailManager->sendContactRequest(
            [
                'email' => $email,
                'message' => $command->message,
            ],
            [$channel->getContactEmail()],
            $channel,
            $command->localeCode,
        );
    }
}
