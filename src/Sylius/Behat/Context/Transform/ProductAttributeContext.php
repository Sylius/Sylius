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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeTranslationInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductAttributeContext implements Context
{
    public function __construct(private RepositoryInterface $productAttributeTranslationRepository)
    {
    }

    /**
     * @Transform :attribute
     * @Transform :productAttribute
     */
    public function getProductAttributeByName(string $name): ProductAttributeInterface
    {
        /** @var ProductAttributeTranslationInterface[] $productAttributeTranslations */
        $productAttributeTranslations = $this->productAttributeTranslationRepository->findBy(['name' => $name]);

        Assert::notEmpty(
            $productAttributeTranslations,
            sprintf('Product attribute with with name "%s" does not exist', $name),
        );

        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $productAttributeTranslations[0]->getTranslatable();

        return $productAttribute;
    }
}
