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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Taxon;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon\AllTaxonsInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Sylius\TwigHooks\LiveComponent\HookableLiveComponentTrait;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
class TreeComponent
{
    use DefaultActionTrait;
    use HookableLiveComponentTrait;

    public function __construct(
        private readonly AllTaxonsInterface $allTaxons,
        private readonly TaxonRepositoryInterface $taxonRepository,
        private readonly ObjectManager $taxonManager,
    ) {
    }

    /** @return array<array-key, mixed> */
    public function getTree(): array
    {
        return $this->buildTree($this->allTaxons->getArrayResult());
    }

    #[LiveAction]
    public function moveUp(#[LiveArg] int $taxonId): void
    {
        $taxonToBeMoved = $this->taxonRepository->find($taxonId);

        if ($taxonToBeMoved->getPosition() > 0) {
            $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() - 1);
            $this->taxonManager->flush();
        }
    }

    #[LiveAction]
    public function moveDown(#[LiveArg] int $taxonId): void
    {
        $taxonToBeMoved = $this->taxonRepository->find($taxonId);

        $taxonToBeMoved->setPosition($taxonToBeMoved->getPosition() + 1);
        $this->taxonManager->flush();
    }

    /**
     * @param array<array-key, mixed> $taxons
     *
     * @return array<array-key, mixed>
     */
    private function buildTree(array $taxons): array
    {
        $tree = [];
        $children = [];

        foreach ($taxons as $taxon) {
            $treeChild = [
                'id' => $taxon['id'],
                'name' => $taxon['name'],
                'children' => $children[$taxon['id']] ?? [],
            ];

            if (null !== $taxon['parent_id']) {
                $children[$taxon['parent_id']][] = $treeChild;
            } else {
                $tree[] = $treeChild;
            }
        }

        return $tree;
    }
}
