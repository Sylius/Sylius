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

namespace Sylius\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxonContext implements Context
{
    /** @var TaxonRepositoryInterface */
    private $taxonRepository;

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
     * @Transform /^taxon "([^"]+)"$/
     * @Transform :taxon
     */
    public function getTaxonByName($name)
    {
        $taxons = $this->taxonRepository->findByName($name, 'en_US');

        Assert::eq(
            count($taxons),
            1,
            sprintf('%d taxons has been found with name "%s".', count($taxons), $name)
        );

        return $taxons[0];
    }

    /**
     * @Transform /^taxon with "([^"]+)" code$/
     */
    public function getTaxonByCode($code)
    {
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);
        Assert::notNull($taxon, sprintf('Taxon with code "%s" does not exist.', $code));

        return $taxon;
    }

    /**
     * @Transform /^classified as "([^"]+)" or "([^"]+)"$/
     * @Transform /^configured with "([^"]+)" and "([^"]+)"$/
     */
    public function getTaxonsByNames(string $firstTaxonName, string $secondTaxonName): array
    {
        return [
            $this->getTaxonByName($firstTaxonName),
            $this->getTaxonByName($secondTaxonName),
        ];
    }
}
