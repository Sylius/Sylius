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
use Sylius\Component\Core\Model\ChannelInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<ChannelInterface>
 */
final readonly class PersistProcessor implements ProcessorInterface
{
    public function __construct(
        private ProcessorInterface $persistProcessor,
    ) {
    }

    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        Assert::isInstanceOf($data, ChannelInterface::class);

        if ($operation instanceof DeleteOperationInterface) {
            return;
        }

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
