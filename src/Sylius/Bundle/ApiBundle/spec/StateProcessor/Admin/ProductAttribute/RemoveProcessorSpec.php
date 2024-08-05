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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductAttribute;

use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ProductAttributeCannotBeRemoved;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class RemoveProcessorSpec extends ObjectBehavior
{
    function let(ProcessorInterface $removeProcessor): void
    {
        $this->beConstructedWith($removeProcessor);
    }

    function it_is_a_processor_interface(): void
    {
        $this->shouldImplement(ProcessorInterface::class);
    }

    public function it_processes_remove_operation(
        ProcessorInterface $removeProcessor,
        Operation $operation,
        ProductAttributeInterface $productAttribute,
    ) {
        $operation->implement(DeleteOperationInterface::class);
        $removeProcessor->process($productAttribute, $operation, [], [])->willReturn(null);

        $this->process($productAttribute, $operation, [], [])->shouldReturn(null);
    }

    public function it_throws_an_exception_when_foreign_key_constraint_violation_occurs(
        ProcessorInterface $removeProcessor,
        Operation $operation,
        ProductAttributeInterface $productAttribute,
    ) {
        $operation->implement(DeleteOperationInterface::class);
        $removeProcessor->process($productAttribute, $operation, [], [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(ProductAttributeCannotBeRemoved::class)->during('process', [$productAttribute, $operation, [], []]);
    }

    public function it_throws_exception_if_operation_is_not_delete(
        Operation $operation,
        ProductAttributeInterface $productAttribute,
    ) {
        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$productAttribute, $operation, [], []]);
    }

    public function it_throws_exception_if_data_is_not_product_attribute_interface(
        Operation $operation,
        \stdClass $nonProduct,
    ) {
        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$nonProduct, $operation, [], []]);
    }
}
