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

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Bundle\ApiBundle\Command\Account\ResendVerificationEmail;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

trigger_deprecation(
    'sylius/api-bundle',
    '1.14',
    'The "%s" class is deprecated and will be removed in Sylius 2.0.',
    ShopCustomerDataPersister::class,
);

/** @deprecated since Sylius 1.14 and will be removed in Sylius 2.0. */
final class ShopCustomerDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private MessageBusInterface $commandBus,
    ) {
    }

    /** @param array<string, mixed> $context */
    public function supports($data, array $context = []): bool
    {
        return $this->decoratedDataPersister->supports($data, $context);
    }

    /** @param array<string, mixed> $context */
    public function persist($data, array $context = [])
    {
        if (!$data instanceof CustomerInterface || 'shop_put' !== ($context['item_operation_name'] ?? null)) {
            return $this->decoratedDataPersister->persist($data, $context);
        }

        $user = $data->getUser();
        if (!$user instanceof ShopUserInterface) {
            return $this->decoratedDataPersister->persist($data, $context);
        }

        Assert::methodExists($user, 'getUsername');
        if ($data->getEmail() === $user->getUsername()) {
            return $this->decoratedDataPersister->persist($data, $context);
        }

        $user->setVerifiedAt(null);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        if (!$channel->isAccountVerificationRequired()) {
            return $this->decoratedDataPersister->persist($data, $context);
        }

        $user->setEnabled(false);

        $result = $this->decoratedDataPersister->persist($data, $context);

        $command = new ResendVerificationEmail();
        $command->setShopUserId($user->getId());
        $command->setChannelCode($channel->getCode());
        $command->setLocaleCode($this->localeContext->getLocaleCode());

        $this->commandBus->dispatch($command);

        return $result;
    }

    /** @param array<string, mixed> $context */
    public function remove($data, array $context = []): void
    {
        $this->decoratedDataPersister->remove($data, $context);
    }
}
