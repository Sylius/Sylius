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

namespace Sylius\Bundle\ApiBundle\Validator\Constraints;

use Sylius\Bundle\ApiBundle\Command\Cart\AddItemToCart;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Webmozart\Assert\Assert;

/** @experimental */
final class ProductOrVariantEnabledValidator extends ConstraintValidator
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function validate($value, Constraint $constraint)
    {
        Assert::isInstanceOf($value, AddItemToCart::class);

        /** @var ProductOrVariantEnabled $constraint */
        Assert::isInstanceOf($constraint, ProductOrVariantEnabled::class);

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->productVariantRepository->findOneBy(['code' =>$value->productVariantCode]);

        if (!$productVariant->getProduct()->isEnabled()) {
            $this->context->addViolation(
                $constraint->message,
                ['%productName%' => $productVariant->getProduct()->getName()]
            );

            return;
        }

        if (!$productVariant->isEnabled()) {
            $this->context->addViolation(
                $constraint->message,
                ['%productName%' => $productVariant->getName()]
            );
        }
    }
}
