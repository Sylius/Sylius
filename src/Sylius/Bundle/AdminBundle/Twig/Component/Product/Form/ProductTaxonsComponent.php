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

namespace Sylius\Bundle\AdminBundle\Twig\Component\Product\Form;

use Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon\AllTaxonsInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
class ProductTaxonsComponent
{
    public function __construct(private readonly AllTaxonsInterface $allTaxons)
    {
    }

    /** @return array<array-key, mixed> */
    public function getTree(): array
    {
        return $this->buildTree($this->allTaxons->getArrayResult());
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
                'id' => $taxon['code'],
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
