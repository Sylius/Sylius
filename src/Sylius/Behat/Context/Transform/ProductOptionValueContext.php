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
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class ProductOptionValueContext implements Context
{
    public function __construct(private RepositoryInterface $productOptionValueRepository)
    {
    }

    /**
     * @Transform /^"([^"]+)" option value$/
     * @Transform :optionValue
     * @Transform :productOptionValue
     */
    public function getProductOptionValueByCode(string $code): ProductOptionValueInterface
    {
        $productOptionValues = $this->productOptionValueRepository->findBy(['code' => $code]);

        Assert::count(
            $productOptionValues,
            1,
            sprintf('%d product option values have been found with name "%s" but should be only one.', count($productOptionValues), $code),
        );

        return $productOptionValues[0];
    }
}
