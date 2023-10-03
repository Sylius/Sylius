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

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform\Messenger;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\ApiPlatform\Messenger\Processor;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\StateProcessor\Command\CommandAwareInputDataProcessorInterface;

final class ProcessorSpec extends ObjectBehavior
{
    function let(
        CommandAwareInputDataProcessorInterface $commandDataInputProcessor,
        ProcessorInterface $decoratedProcessor,
    ): void {
        $this->beConstructedWith($commandDataInputProcessor, $decoratedProcessor);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(Processor::class);
    }

    function it_calls_command_aware_input_data_processor_and_decorated_processor(
        CommandAwareInputDataProcessorInterface $commandDataInputProcessor,
        ProcessorInterface $decoratedProcessor,
        Operation $operation,
        SendContactRequest $data,
        SendContactRequest $processedData,
        SendContactRequest $finalResult,
    ): void {
        $commandDataInputProcessor->process($data, $operation, ['id' => 1], ['foo' => 'bar'])->willReturn($processedData);
        $decoratedProcessor->process($processedData, $operation, ['id' => 1], ['foo' => 'bar'])->willReturn($finalResult);

        $this->process($data, $operation, ['id' => 1], ['foo' => 'bar'])->shouldReturn($finalResult);
    }
}
