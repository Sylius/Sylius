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

namespace Sylius\Bundle\CoreBundle\Validator\Constraints;

use Sylius\Bundle\OrderBundle\Controller\AddToCartCommandInterface;
use Sylius\Component\Core\Model\OrderItem;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class CartItemVariantEnabledValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CartItemVariantEnabled) {
            throw new UnexpectedTypeException($constraint, CartItemVariantEnabled::class);
        }

        if (!$value instanceof AddToCartCommandInterface) {
            throw new UnexpectedValueException($value, AddToCartCommandInterface::class);
        }

        /** @var OrderItem $cartItem */
        $cartItem = $value->getCartItem();

        if (null === $variant = $cartItem->getVariant()) {
            return;
        }

        if (!$variant->isEnabled()) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%variantName%', $variant->getInventoryName())
                ->atPath('cartItem.variant')
                ->addViolation()
            ;
        }
    }
}
