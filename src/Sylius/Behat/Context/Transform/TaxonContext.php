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
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonContext implements Context
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * @Transform /^classified as "([^"]+)"$/
     * @Transform /^belongs to "([^"]+)"$/
     * @Transform /^"([^"]+)" taxon$/
     * @Transform /^"([^"]+)" as a parent taxon$/
     * @Transform /^"([^"]+)" parent taxon$/
     * @Transform /^parent taxon to "([^"]+)"$/
     * @Transform /^taxon with "([^"]+)" name/
     */
    public function getTaxonByName($taxonName)
    {
        return $this->getTaxonBy(['name' => $taxonName]);
    }

    /**
     * @Transform /^taxon with "([^"]+)" code$/
     */
    public function getTaxonByCode($code)
    {
        return $this->getTaxonBy(['code' => $code]);
    }

    /**
     * @Transform /^classified as "([^"]+)" or "([^"]+)"$/
     */
    public function getTaxonsByNames($firstTaxon, $secondTaxon)
    {
        return [
            $this->getTaxonByName($firstTaxon),
            $this->getTaxonByName($secondTaxon)
        ];
    }

    /**
     * @param array $parameters
     *
     * @return object
     */
    private function getTaxonBy(array $parameters)
    {
        $taxon = $this->taxonRepository->findOneBy($parameters);
        Assert::notNull($taxon, 'Taxon does not exist.');

        return $taxon;
    }
}
