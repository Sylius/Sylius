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

namespace Sylius\Bundle\ApiBundle\StateProcessor\Admin\Channel;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\Exception\ChannelCannotBeRemoved;
use Sylius\Component\Channel\Checker\ChannelDeletionCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

/** @implements ProcessorInterface<ChannelInterface> */
final readonly class RemoveProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $removeProcessor,
        private ChannelDeletionCheckerInterface $channelDeletionChecker,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        Assert::isInstanceOf($data, ChannelInterface::class);
        Assert::isInstanceOf($operation, DeleteOperationInterface::class);

        if (!$this->channelDeletionChecker->isDeletable($data)) {
            throw new ChannelCannotBeRemoved('The channel cannot be deleted. At least one enabled channel is required.', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->removeProcessor->process($data, $operation, $uriVariables, $context);
    }
}
