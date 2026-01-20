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

namespace Sylius\Bundle\TaxonomyBundle\Provider;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Sylius\Component\Taxonomy\Exception\TaxonNotFoundException;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Provider\TaxonTreeProviderInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

final readonly class TaxonTreeProvider implements TaxonTreeProviderInterface
{
    /**
     * @param TaxonRepositoryInterface<TaxonInterface> $taxonRepository
     * @param NestedTreeRepository<TaxonInterface> $taxonTreeRepository
     */
    public function __construct(
        private TaxonRepositoryInterface $taxonRepository,
        private NestedTreeRepository $taxonTreeRepository,
    ) {
    }

    public function getBranchWith(string $taxonCode, bool $includeRoot = false): array
    {
        $taxon = $this->getTaxon($taxonCode);

        return self::filterRootTaxon(array_merge(
            $this->taxonTreeRepository->getPath($taxon),
            $this->taxonTreeRepository->getChildren($taxon),
        ), $includeRoot);
    }

    public function getPathTo(string $taxonCode, bool $includeRoot = false): array
    {
        return self::filterRootTaxon(
            $this->taxonTreeRepository->getPath($this->getTaxon($taxonCode)),
            $includeRoot,
        );
    }

    private function getTaxon(string $code): TaxonInterface
    {
        $taxon = $this->taxonRepository->findOneBy(['code' => $code]);
        if (null === $taxon) {
            throw TaxonNotFoundException::withCode($code);
        }

        return $taxon;
    }

    /**
     * @param TaxonInterface[] $taxons
     *
     * @return TaxonInterface[]
     */
    private static function filterRootTaxon(array $taxons, bool $includeRoot): array
    {
        if ($includeRoot) {
            return $taxons;
        }

        return array_values(array_filter($taxons, fn (TaxonInterface $taxon) => !$taxon->isRoot()));
    }
}
