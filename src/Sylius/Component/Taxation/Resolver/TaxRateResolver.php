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

namespace Sylius\Component\Taxation\Resolver;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Taxation\Checker\TaxRateDateEligibilityCheckerInterface;
use Sylius\Component\Taxation\Model\TaxableInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

class TaxRateResolver implements TaxRateResolverInterface
{
    public function __construct(
        protected RepositoryInterface $taxRateRepository,
        protected ?TaxRateDateEligibilityCheckerInterface $taxRateDateChecker = null
    ) {
        if ($this->taxRateDateChecker === null) {
            @trigger_error('Not passing TaxRateDateEligibilityCheckerInterface through constructor is deprecated in Sylius 1.12 and it will be prohibited in Sylius 2.0');
        }
    }

    public function resolve(TaxableInterface $taxable, array $criteria = []): ?TaxRateInterface
    {
        if (null === $category = $taxable->getTaxCategory()) {
            return null;
        }

        $criteria = array_merge(['category' => $category], $criteria);

        if ($this->taxRateDateChecker) {
            $taxRates = $this->taxRateRepository->findBy($criteria);

            return $this->provideEligibleTaxRate($taxRates);
        }

        return $this->taxRateRepository->findOneBy($criteria);
    }

    private function provideEligibleTaxRate(array $taxRates): ?TaxRateInterface
    {
        foreach ($taxRates as $taxRate) {
            if ($this->taxRateDateChecker->isEligible($taxRate)) {
                return $taxRate;
            }
        }

        return null;
    }
}
