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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\PaymentMethod;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeRemoved;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;

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
        PaymentMethodInterface $paymentMethod,
    ): void {
        $operation->implement(DeleteOperationInterface::class);
        $removeProcessor->process($paymentMethod, $operation, [], [])->shouldBeCalled();

        $this->process($paymentMethod, $operation);
    }

    public function it_throws_an_exception_when_foreign_key_constraint_violation_occurs(
        ProcessorInterface $removeProcessor,
        Operation $operation,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $operation->implement(DeleteOperationInterface::class);
        $removeProcessor->process($paymentMethod, $operation, [], [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(PaymentMethodCannotBeRemoved::class)->during('process', [$paymentMethod, $operation]);
    }

    public function it_throws_an_exception_if_operation_is_not_delete(
        ShippingMethodInterface $shippingMethod,
    ): void {
        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [$shippingMethod, new Get()]);
    }

    public function it_throws_exception_if_data_is_not_product_interface(Operation $operation): void
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('process', [new \stdClass(), new Delete()]);
    }
}
