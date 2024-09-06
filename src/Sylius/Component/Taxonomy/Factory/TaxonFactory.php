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

namespace Sylius\Component\Taxonomy\Factory;

use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Resource\Factory\FactoryInterface;

/**
 * @template T of TaxonInterface
 *
 * @implements TaxonFactoryInterface<T>
 */
final class TaxonFactory implements TaxonFactoryInterface
{
    /** @param FactoryInterface<T> $factory */
    public function __construct(private FactoryInterface $factory)
    {
    }

    public function createNew(): TaxonInterface
    {
        return $this->factory->createNew();
    }

    public function createForParent(TaxonInterface $parent): TaxonInterface
    {
        $taxon = $this->createNew();
        $taxon->setParent($parent);

        return $taxon;
    }
}
