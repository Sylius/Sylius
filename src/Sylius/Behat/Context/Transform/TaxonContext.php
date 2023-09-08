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
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Webmozart\Assert\Assert;

final class TaxonContext implements Context
{
    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private string $locale,
    ) {
    }

    /**
     * @Transform /^classified as "([^"]+)"$/
     * @Transform /^belongs to "([^"]+)"$/
     * @Transform /^"([^"]+)" taxon$/
     * @Transform /^"([^"]+)" as a parent taxon$/
     * @Transform /^"([^"]+)" parent taxon$/
     * @Transform /^parent taxon to "([^"]+)"$/
     * @Transform /^taxon should be "([^"]+)"$/
     * @Transform /^taxon with "([^"]+)" name/
     * @Transform /^taxon "([^"]+)"$/
     * @Transform :taxon
     * @Transform :parentTaxon
     */
    public function getTaxonByName(string $name): TaxonInterface
    {
        $taxons = $this->taxonRepository->findByName($name, $this->locale);

        Assert::eq(
            count($taxons),
            1,
            sprintf('%d taxons has been found with name "%s".', count($taxons), $name),
        );

        return $taxons[0];
    }

    /**
     * @Transform /^taxon with "([^"]+)" code$/
     */
    public function getTaxonByCode(string $code): TaxonInterface
    {
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);
        Assert::notNull($taxon, sprintf('Taxon with code "%s" does not exist.', $code));

        return $taxon;
    }

    /**
     * @Transform /^classified as "([^"]+)" or "([^"]+)"$/
     * @Transform /^configured with "([^"]+)" and "([^"]+)"$/
     * @Transform /^"([^"]+)" and "([^"]+)" taxons$/
     * @Transform /^belongs to "([^"]+)" and "([^"]+)"/
     * @Transform /^"([^"]+)" and "([^"]+)" in the vertical menu$/
     */
    public function getTaxonsByNames(string ...$taxonNames): iterable
    {
        foreach ($taxonNames as $taxonName) {
            yield $this->getTaxonByName($taxonName);
        }
    }
}
