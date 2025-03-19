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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Common;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Model\Promotion;
use Sylius\Component\Core\Model\ShippingMethod;

final class ResourceRemoveProcessorSpec extends ObjectBehavior
{
    public function let(ProcessorInterface $decoratedRemoveProcessor)
    {
        $this->beConstructedWith($decoratedRemoveProcessor);
    }

    public function it_processes_data_without_exceptions(ProcessorInterface $decoratedRemoveProcessor, Operation $operation): void
    {
        $data = new Promotion();
        $decoratedRemoveProcessor->process($data, $operation, [], [])->shouldBeCalled();

        $this->process($data, $operation, [], []);
    }

    public function it_throws_a_resource_delete_exception_on_foreign_key_violation(ProcessorInterface $decoratedRemoveProcessor, Operation $operation): void
    {
        $data = new ShippingMethod();
        $decoratedRemoveProcessor->process($data, $operation, [], [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(ResourceDeleteException::class)->during('process', [$data, $operation, [], []]);
    }
}
