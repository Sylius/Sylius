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
final class ProductVariantExistValidator extends ConstraintValidator
{
    /** @var ProductVariantRepositoryInterface */
    private $productVariantRepository;

    public function __construct(ProductVariantRepositoryInterface $productVariantRepository)
    {
        $this->productVariantRepository = $productVariantRepository;
    }

    public function validate($value, Constraint $constraint): void
    {
        Assert::isInstanceOf($value, AddItemToCart::class);

        /** @var ProductVariantExist $constraint */
        Assert::isInstanceOf($constraint, ProductVariantExist::class);

        /** @var ProductVariantInterface|null $productVariant */
        if ($this->productVariantRepository->findOneBy(['code' =>$value->productVariantCode]) === null) {
            $this->context->addViolation(
                $constraint->message,
                ['%productVariantCode%' => $value->productVariantCode]
            );
        }
    }
}
