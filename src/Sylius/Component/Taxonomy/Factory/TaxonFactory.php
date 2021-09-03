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

namespace Sylius\Component\Taxonomy\Factory;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class TaxonFactory implements TaxonFactoryInterface
{
    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
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
