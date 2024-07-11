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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Channel;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\ChannelInterface;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $persistProcessor,
    ): void {
        $this->beConstructedWith($persistProcessor);
    }

    function it_throws_an_exception_if_object_is_not_a_channel(
        ProcessorInterface $persistProcessor,
    ): void {
        $persistProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), new Delete(), [], []])
        ;
    }

    function it_uses_decorated_data_persister_to_persist_channel(
        ProcessorInterface $persistProcessor,
        ChannelInterface $channel,
    ): void {
        $operation = new Post();
        $uriVariables = [];
        $context = [];

        $persistProcessor->process($channel, $operation, $uriVariables, $context)->willReturn($channel);

        $this->process($channel, $operation, $uriVariables, $context)->shouldReturn($channel);
    }
}
