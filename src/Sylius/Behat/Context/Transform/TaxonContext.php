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
     * @Transform classified as :taxonName
     * @Transform belongs to :taxonName
     */
    public function getTaxonByName($taxonName)
    {
        $taxon = $this->taxonRepository->findOneByName($taxonName);
        if (null === $taxon) {
            throw new \InvalidArgumentException(sprintf('Taxon with name "%s" does not exist.', $taxonName));
        }

        return $taxon;
    }
}
