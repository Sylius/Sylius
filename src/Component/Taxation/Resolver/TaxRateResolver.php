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

namespace Sylius\Component\Taxation\Resolver;

use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

class TaxRateResolver implements TaxRateResolverInterface
{
    /** @param RepositoryInterface<TaxRateInterface> $taxRateRepository */
    public function __construct(
        protected RepositoryInterface $taxRateRepository,
        protected TaxRateDateEligibilityCheckerInterface $taxRateDateChecker,
    ) {
    }

    public function resolve(TaxableInterface $taxable, array $criteria = []): ?TaxRateInterface
    {
        if (null === $category = $taxable->getTaxCategory()) {
            return null;
        }

        $criteria = array_merge(['category' => $category], $criteria);

        $taxRates = $this->taxRateRepository->findBy($criteria);

        foreach ($taxRates as $taxRate) {
            if ($this->taxRateDateChecker->isEligible($taxRate)) {
                return $taxRate;
            }
        }

        return null;
    }
}
