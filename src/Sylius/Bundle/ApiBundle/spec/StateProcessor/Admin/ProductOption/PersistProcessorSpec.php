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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\ProductOption;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\ProductOptionValueCannotBeRemoved;
use Sylius\Component\Core\Exception\ResourceDeleteException;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;

final class PersistProcessorSpec extends ObjectBehavior
{
    function let(ProcessorInterface $processor): void
    {
        $this->beConstructedWith($processor);
    }

    function it_is_a_processor_interface(): void
    {
        $this->shouldImplement(ProcessorInterface::class);
    }

    public function it_processes_remove_operation(
        ProcessorInterface $processor,
        ProductOptionInterface $productOption,
    ): void {
        $operation = new Put();
        $processor->process($productOption, $operation, [], [])->shouldBeCalled();

        $this->process($productOption, $operation);
    }

    public function it_throws_an_exception_when_resource_delete_exception_occurs(
        ProcessorInterface $processor,
        ProductOptionInterface $productOption,
    ): void {
        $operation = new Put();
        $processor->process($productOption, $operation, [], [])->willThrow(ResourceDeleteException::class);

        $this->shouldThrow(ProductOptionValueCannotBeRemoved::class)->during('process', [$productOption, $operation]);
    }

    public function it_throws_an_exception_if_operation_is_not_put(
        ShippingMethodInterface $shippingMethod,
    ): void {
        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$shippingMethod, new Delete()]);
    }

    public function it_throws_exception_if_data_is_not_product_option_interface(): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [new \stdClass(), new Put()]);
    }
}
