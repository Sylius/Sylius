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

namespace spec\Sylius\Bundle\ApiBundle\ApiPlatform\StateProcessor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\ApiPlatform\StateProcessor\MessengerProcessor;
use Sylius\Bundle\ApiBundle\Command\SendContactRequest;
use Sylius\Bundle\ApiBundle\StateProcessor\Input\InputDataProcessorInterface;

final class MessengerProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $decoratedProcessor,
        InputDataProcessorInterface $inputDataProcessor1,
        InputDataProcessorInterface $inputDataProcessor2,
    ): void {
        $this->beConstructedWith($decoratedProcessor, [$inputDataProcessor1, $inputDataProcessor2]);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(MessengerProcessor::class);
    }

    function it_calls_supported_input_data_processors_and_decorated_one_afterwards(
        InputDataProcessorInterface $inputDataProcessor1,
        InputDataProcessorInterface $inputDataProcessor2,
        ProcessorInterface $decoratedProcessor,
        Operation $operation,
        SendContactRequest $data,
        SendContactRequest $processedData,
        SendContactRequest $finalResult,
    ): void {
        $inputDataProcessor1->supports($data, $operation, ['id' => 1], ['foo' => 'bar'])->willReturn(true);
        $inputDataProcessor1->process($data, $operation, ['id' => 1], ['foo' => 'bar'])->willReturn([$processedData, $operation, ['id' => 1], ['foo' => 'bar']]);

        $inputDataProcessor2->supports($processedData, $operation, ['id' => 1], ['foo' => 'bar'])->willReturn(false);
        $inputDataProcessor2->process($processedData, $operation, ['id' => 1], ['foo' => 'bar'])->shouldNotBeCalled();

        $decoratedProcessor->process($processedData, $operation, ['id' => 1], ['foo' => 'bar'])->willReturn($finalResult);

        $this->process($data, $operation, ['id' => 1], ['foo' => 'bar'])->shouldReturn($finalResult);
    }
}
