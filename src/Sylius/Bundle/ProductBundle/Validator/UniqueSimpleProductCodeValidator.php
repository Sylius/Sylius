<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Validator;

use Sylius\Bundle\ProductBundle\Validator\Constraint\UniqueSimpleProductCode;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

final class UniqueSimpleProductCodeValidator extends ConstraintValidator
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        /** @var UniqueSimpleProductCode $constraint */
        Assert::isInstanceOf($constraint, UniqueSimpleProductCode::class);

        if (!$value instanceof ProductInterface) {
            throw new UnexpectedTypeException($value, ProductInterface::class);
        }

        if (!$value->isSimple()) {
            return;
        }

        /** @var ProductVariantInterface $existingProductVariant */
        $existingProductVariant = $this->productVariantRepository->findOneBy(['code' => $value->getCode()]);

        if (null !== $existingProductVariant && $existingProductVariant->getProduct()->getId() !== $value->getId()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('code')
                ->addViolation()
            ;
        }
    }
}
