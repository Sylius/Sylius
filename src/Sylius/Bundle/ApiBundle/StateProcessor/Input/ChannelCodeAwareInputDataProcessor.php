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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Input;

use ApiPlatform\Metadata\Operation;
use Sylius\Bundle\ApiBundle\Command\ChannelCodeAwareInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

/** @experimental */
final readonly class ChannelCodeAwareInputDataProcessor implements InputDataProcessorInterface
{
    public function __construct(private ChannelContextInterface $channelContext)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $channel = $this->channelContext->getChannel();

        $data->setChannelCode($channel->getCode());

        return [$data, $operation, $uriVariables, $context];
    }

    public function supports(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): bool
    {
        return $data instanceof ChannelCodeAwareInterface;
    }
}
