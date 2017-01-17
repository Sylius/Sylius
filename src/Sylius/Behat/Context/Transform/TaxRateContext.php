<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class TaxRateContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $taxRateRepository;

    /**
     * @param RepositoryInterface $taxRateRepository
     */
    public function __construct(RepositoryInterface $taxRateRepository)
    {
        $this->taxRateRepository = $taxRateRepository;
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
            sprintf('Tax rate with name "%s" does not exist', $taxRateName)
        );

        return $taxRate;
    }
}
