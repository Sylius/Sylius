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
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxRateContext implements Context
{
    public function __construct(private RepositoryInterface $taxRateRepository)
    {
    }

    /**
     * @Transform :taxRate
     * @Transform /^"([^"]+)" tax rate$/
     */
    public function getTaxRateByName($taxRateName)
    {
        $taxRate = $this->taxRateRepository->findOneBy(['name' => $taxRateName]);

        Assert::notNull(
            $taxRate,
            sprintf('Tax rate with name "%s" does not exist', $taxRateName),
        );

        return $taxRate;
    }
}
