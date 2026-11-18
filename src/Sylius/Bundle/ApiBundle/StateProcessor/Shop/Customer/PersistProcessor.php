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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Shop\Customer;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Command\Account\RequestShopUserVerification;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<CustomerInterface> */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
        private MessageBusInterface $commandBus,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, CustomerInterface::class);
        Assert::notInstanceOf($operation, DeleteOperationInterface::class);

        $user = $data->getUser();
        if (!$user instanceof ShopUserInterface || $data->getEmail() === $user->getUsername()) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $user->setVerifiedAt(null);

        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();
        if (!$channel->isAccountVerificationRequired()) {
            return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
        }

        $user->setEnabled(false);

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        $this->commandBus->dispatch(new RequestShopUserVerification(
            $user->getId(),
            $channel->getCode(),
            $this->localeContext->getLocaleCode(),
        ));

        return $result;
    }
}
