<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Validator;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class UniqueSimpleProductCodeValidator extends ConstraintValidator
{
    /**
     * @var ProductVariantRepositoryInterface
     */
    private $productVariantRepository;

    /**
     * @param ProductVariantRepositoryInterface $productVariantRepository
     */
    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof ProductInterface) {
            throw new UnexpectedTypeException($value, ProductInterface::class);
        }

        if (!$value->isSimple()) {
            return;
        }

        $existingProductVariant = $this->productVariantRepository->findOneBy(['code' => $value->getCode()]);

        if (null !== $existingProductVariant && $existingProductVariant->getProduct()->getId() !== $value->getId()) {
            $this->context->buildViolation($constraint->message)
                ->atPath('code')
                ->addViolation()
            ;
        }
    }
}
