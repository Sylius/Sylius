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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class OrderAddressRequirementValidator extends ConstraintValidator
{
    /** @param OrderRepositoryInterface<OrderInterface> $orderRepository */
    public function __construct(private readonly OrderRepositoryInterface $orderRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof OrderAddressRequirement) {
            throw new UnexpectedTypeException($constraint, OrderAddressRequirement::class);
        }

        if (!$value instanceof UpdateCart) {
            throw new UnexpectedValueException($value, UpdateCart::class);
        }

        if (null === $value->billingAddress && null === $value->shippingAddress) {
            return;
        }

        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->findCartByTokenValue($value->orderTokenValue);
        $channel = $order?->getChannel();
        if (null === $channel) {
            throw new ChannelNotFoundException();
        }

        [$method, $addressName] = $channel->isShippingAddressInCheckoutRequired() ? ['getShippingAddress', 'shipping address'] : ['getBillingAddress', 'billing address'];

        if (null === $value->$method()) {
            $this->context->addViolation($constraint->message, ['%addressName%' => $addressName]);
        }
    }
}
