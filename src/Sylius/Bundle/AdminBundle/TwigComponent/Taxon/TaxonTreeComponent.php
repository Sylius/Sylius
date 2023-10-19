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

namespace Sylius\Bundle\AdminBundle\TwigComponent\Taxon;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

final readonly class TaxonTreeComponent
{
    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     */
    public function __construct (private TaxonRepositoryInterface $taxonRepository)
    {
    }

    /**
     * @return array<TaxonInterface>
     */
    #[ExposeInTemplate]
    public function getRootNodes(): array
    {
        return $this->taxonRepository->findHydratedRootNodes();
    }
}
