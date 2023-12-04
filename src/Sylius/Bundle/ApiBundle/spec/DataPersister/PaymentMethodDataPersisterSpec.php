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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeRemoved;
use Sylius\Component\Core\Model\PaymentMethodInterface;

final class PaymentMethodDataPersisterSpec extends ObjectBehavior
{
    function let(ContextAwareDataPersisterInterface $dataPersister): void
    {
        $this->beConstructedWith($dataPersister);
    }

    function it_is_a_context_aware_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_supports_only_payment_method(PaymentMethodInterface $paymentMethod): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($paymentMethod)->shouldReturn(true);
    }

    function it_uses_inner_persister_to_persist_payment_method(
        ContextAwareDataPersisterInterface $dataPersister,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $dataPersister->persist($paymentMethod, [])->shouldBeCalled();

        $this->persist($paymentMethod);
    }

    function it_throws_cannot_be_removed_exception_if_constraint_fails_on_removal(
        ContextAwareDataPersisterInterface $dataPersister,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $dataPersister->remove($paymentMethod, [])->willThrow(ForeignKeyConstraintViolationException::class);

        $this->shouldThrow(PaymentMethodCannotBeRemoved::class)->during('remove', [$paymentMethod]);
    }

    function it_uses_inner_persister_to_remove_payment_method(
        ContextAwareDataPersisterInterface $dataPersister,
        PaymentMethodInterface $paymentMethod,
    ): void {
        $dataPersister->remove($paymentMethod, [])->shouldBeCalled();

        $this->remove($paymentMethod);
    }
}
