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

use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ProductVariantCodeExistsValidator extends ConstraintValidator
{
    /** @param ProductVariantRepositoryInterface<ProductVariantInterface> $productVariantRepository */
    public function __construct(private ProductVariantRepositoryInterface $productVariantRepository)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ProductVariantCodeExists) {
            throw new UnexpectedTypeException($constraint, ProductVariantCodeExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if ($this->productVariantRepository->findOneBy(['code' => $value]) === null) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ code }}', $value)
                ->addViolation()
            ;
        }
    }
}
