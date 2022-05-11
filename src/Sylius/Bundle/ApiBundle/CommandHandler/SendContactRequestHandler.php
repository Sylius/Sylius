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

namespace Sylius\Bundle\ApiBundle\CommandHandler;

use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ShopBundle\EmailManager\ContactEmailManagerInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Webmozart\Assert\Assert;

/** experimental */
final class SendContactRequestHandler implements MessageHandlerInterface
{
    public function __construct(
        private ContactEmailManagerInterface $contactEmailManager,
        private ChannelRepositoryInterface $channelRepository,
    ) {
    }

    public function __invoke(SendContactRequest $command): void
    {
        $channel = $this->channelRepository->findOneByCode($command->getChannelCode());
        Assert::notNull($channel);

        $this->contactEmailManager->sendContactRequest(
            ['message' => $command->getMessage(), 'email' => $command->getEmail()],
            [$channel->getContactEmail()],
            $channel,
            $command->getLocaleCode()
        );
    }
}
