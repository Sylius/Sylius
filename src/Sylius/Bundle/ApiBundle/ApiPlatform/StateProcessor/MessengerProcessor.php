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

namespace Sylius\Bundle\ApiBundle\ApiPlatform\StateProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Sylius\Bundle\ApiBundle\StateProcessor\Input\InputDataProcessorInterface;

final readonly class MessengerProcessor implements ProcessorInterface
{
    /** @param InputDataProcessorInterface[] $inputDataProcessors */
    public function __construct(
        private ProcessorInterface $decoratedProcessor,
        private iterable $inputDataProcessors,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        foreach ($this->inputDataProcessors as $inputDataProcessor) {
            if ($inputDataProcessor->supports($data, $operation, $uriVariables, $context)) {
                [$data, $operation, $uriVariables, $context] = $inputDataProcessor->process($data, $operation, $uriVariables, $context);
            }
        }

        return $this->decoratedProcessor->process($data, $operation, $uriVariables, $context);
    }
}
