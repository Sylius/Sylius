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

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TaxonContext implements Context
{
    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param RepositoryInterface $taxonRepository
     */
    public function __construct(RepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * @Transform classified as :taxonName
     * @Transform belongs to :taxonName
     */
    public function getTaxonByName($taxonName)
    {
        $taxon = $this->taxonRepository->findOneBy(['name' => $taxonName]);
        if (null === $taxon) {
            throw new \InvalidArgumentException(sprintf('Taxon with name "%s" does not exist.', $taxonName));
        }

        return $taxon;
    }
}
