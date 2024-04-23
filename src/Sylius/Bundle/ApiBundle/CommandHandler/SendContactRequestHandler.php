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
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

final class SendContactRequestHandler implements MessageHandlerInterface
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
        $channel = $this->channelRepository->findOneByCode($command->getChannelCode());

        if ($channel === null) {
            throw new ChannelNotFoundException($command->getChannelCode());
        }

        $email = $command->getEmail();
        Assert::notNull($email);

        $this->contactEmailManager->sendContactRequest(
            [
                'email' => $email,
                'message' => $command->getMessage(),
            ],
            [$channel->getContactEmail()],
            $channel,
            $command->localeCode,
        );
    }
}
