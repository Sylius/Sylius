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

namespace Sylius\Component\Taxonomy\Positioner;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final class TaxonPositioner implements TaxonPositionerInterface
{
    public function __construct (
        private TaxonRepositoryInterface $taxonRepository,
    ) {
    }

    public function moveUp(TaxonInterface $taxon): void
    {
        $taxonAbove = $this->taxonRepository->findOneAbove($taxon);

        if (null !== $taxonAbove) {
            $this->swapTaxonPositions($taxon, $taxonAbove);
        }
    }

    public function moveDown(TaxonInterface $taxon): void
    {
        $taxonBelow = $this->taxonRepository->findOneBelow($taxon);

        if (null !== $taxonBelow) {
            $this->swapTaxonPositions($taxon, $taxonBelow);
        }
    }

    private function swapTaxonPositions(TaxonInterface $firstTaxon, TaxonInterface $secondTaxon): void
    {
        $firstTaxonPosition = $firstTaxon->getPosition();
        $secondTaxonPosition = $secondTaxon->getPosition();

        $firstTaxon->setPosition($secondTaxonPosition);
        $secondTaxon->setPosition($firstTaxonPosition);
    }
}
